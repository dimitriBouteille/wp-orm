<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Orm;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\DetectsLostConnections;
use Illuminate\Database\LostConnectionException;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;

/**
 * @see https://developer.wordpress.org/reference/classes/wpdb/
 */
class Database implements ConnectionInterface
{
    use DetectsLostConnections;

    /**
     * @var \wpdb
     */
    protected \wpdb $db;

    /**
     * Count of active transactions
     * @var int
     */
    public int $transactionCount = 0;

    /**
     * The database connection configuration options.
     * @var array
     */
    protected array $config = [];

    /**
     * @var string|null
     */
    protected ?string $tablePrefix = '';

    /**
     * @var null|Database
     */
    protected static ?self $instance = null;

    /**
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (!self::$instance instanceof \Dbout\WpOrm\Orm\Database) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        global $wpdb;
        if (!$wpdb instanceof \wpdb) {
            throw new \Exception('The global variable $wpdb must be instance of \wpdb.');
        }

        $this->config = [
            'connection_name' => 'wp-eloquent-mysql2',
            // @phpstan-ignore-next-line
            'name' => $wpdb->dbname,
        ];

        $this->tablePrefix = $wpdb->prefix;
        $this->db = $wpdb;
    }

    /**
     * @inheritDoc
     */
    public function getDatabaseName(): string
    {
        return $this->getConfig('name');
    }

    /**
     * Get the database connection name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->getConfig('connection_name');
    }

    /**
     * Get the table prefix for the connection.
     *
     * @return string|null
     */
    public function getTablePrefix(): ?string
    {
        return $this->tablePrefix;
    }

    /**
     * @inheritDoc
     */
    public function table($table, $as = null): Builder
    {
        $table = $this->getTablePrefix() . $table;
        return $this->query()->from($table, $as);
    }

    /**
     * @return Builder
     */
    public function query(): Builder
    {
        return new Builder(
            $this,
            $this->getQueryGrammar(),
            $this->getPostProcessor()
        );
    }

    /**
     * @inheritDoc
     */
    public function selectOne($query, $bindings = [], $useReadPdo = true)
    {
        return $this->run($query, $bindings, function (string $query, array $bindings) {
            $query = $this->bind_params($query, $bindings);
            return $this->db->get_row($query);
        });
    }

    /**
     * @inheritDoc
     */
    public function select($query, $bindings = [], $useReadPdo = true): array
    {
        return $this->run($query, $bindings, function (string $query, array $bindings) {
            $query = $this->bind_params($query, $bindings);
            return $this->db->get_results($query);
        });
    }

    /**
     * Run a select statement against the database and returns a generator.
     * TODO: Implement cursor and all the related sub-methods.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @param  bool  $useReadPdo
     * @return \Generator
     */
    public function cursor($query, $bindings = [], $useReadPdo = true)
    {

    }

    /**
     * A hacky way to emulate bind parameters into SQL query
     *
     * @param $query
     * @param $bindings
     *
     * @return mixed
     */
    private function bind_params($query, $bindings, $update = false)
    {
        $query = \str_replace('"', '`', (string) $query);
        $bindings = $this->prepareBindings($bindings);

        if ($bindings === []) {
            return $query;
        }

        $bindings = \array_map(function ($replace) {
            if (\is_string($replace)) {
                $replace = "'" . esc_sql($replace) . "'";
            } elseif ($replace === null) {
                $replace = "null";
            }

            return $replace;
        }, $bindings);

        $query = \str_replace(['%', '?'], ['%%', '%s'], $query);

        return \vsprintf($query, $bindings);
    }

    /**
     * @param $query
     * @param $bindings
     * @throws \Exception
     * @return never
     * @deprecated Remove in next version.
     */
    public function bind_and_run($query, $bindings = []): never
    {
        throw new \Exception('This function is no longer usable, it will be removed in a future version.');
    }

    /**
     * @inheritDoc
     */
    public function insert($query, $bindings = []): bool
    {
        return $this->statement($query, $bindings);
    }

    /**
     * @inheritDoc
     */
    public function update($query, $bindings = []): int
    {
        return $this->affectingStatement($query, $bindings);
    }

    /**
     * @inheritDoc
     */
    public function delete($query, $bindings = []): int
    {
        return $this->affectingStatement($query, $bindings);
    }

    /**
     * @inheritDoc
     */
    public function statement($query, $bindings = []): bool
    {
        return $this->run($query, $bindings, function (string $query, array $bindings) {
            $query = $this->bind_params($query, $bindings, true);
            return $this->db->query($query);
        });
    }

    /**
     * @inheritDoc
     */
    public function affectingStatement($query, $bindings = []): int
    {
        return $this->run($query, $bindings, function (string $query, array $bindings) {
            $newQuery = $this->bind_params($query, $bindings, true);
            $result = $this->db->query($newQuery);
            if (!is_numeric($result)) {
                return $result;
            }

            return (int) $result;
        });
    }

    /**
     * @inheritDoc
     */
    public function unprepared($query): bool
    {
        return $this->run($query, [], function (string $query) {
            return $this->db->query($query);
        });
    }

    /**
     * @inheritDoc
     */
    public function prepareBindings(array $bindings): array
    {
        $grammar = $this->getQueryGrammar();
        foreach ($bindings as $key => $value) {
            if (is_bool($value)) {
                $bindings[$key] = (int) $value;
            } elseif (is_scalar($value)) {
                continue;
            } elseif ($value instanceof \DateTimeInterface) {
                $bindings[$key] = $value->format($grammar->getDateFormat());
            }
        }

        return $bindings;
    }

    /**
     * @inheritDoc
     */
    public function transaction(\Closure $callback, $attempts = 1)
    {
        $this->beginTransaction();
        try {
            $data = $callback();
            $this->commit();
            return $data;
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     */
    public function beginTransaction(): void
    {
        $transaction = $this->unprepared("START TRANSACTION;");
        if ($transaction) {
            $this->transactionCount++;
        }
    }

    /**
     * @inheritDoc
     */
    public function commit(): void
    {
        if ($this->transactionCount < 1) {
            return;
        }
        $transaction = $this->unprepared("COMMIT;");
        if ($transaction) {
            $this->transactionCount--;
        }
    }

    /**
     * @inheritDoc
     */
    public function rollBack(): void
    {
        if ($this->transactionCount < 1) {
            return;
        }
        $transaction = $this->unprepared("ROLLBACK;");
        if ($transaction) {
            $this->transactionCount--;
        }
    }

    /**
     * @inheritDoc
     */
    public function transactionLevel(): int
    {
        return $this->transactionCount;
    }

    /**
     * @inheritDoc
     */
    public function pretend(\Closure $callback)
    {
        // TODO: Implement pretend() method.
    }

    /**
     * @return Processor
     */
    public function getPostProcessor(): Processor
    {
        return new Processor();
    }

    /**
     * @return Grammar
     */
    public function getQueryGrammar(): Grammar
    {
        return new Grammar();
    }

    /**
     * Return the last insert id
     *
     * @return int|null
     */
    public function lastInsertId(): ?int
    {
        return $this->db->insert_id;
    }

    /**
     * Get an option from the configuration options.
     *
     * @param  string $option
     * @return mixed
     */
    public function getConfig(string $option): mixed
    {
        return Arr::get($this->config, $option);
    }

    /**
     * @return $this
     */
    public function getPdo()
    {
        return $this;
    }

    /**
     * @return bool
     * @see \wpdb::print_error()
     */
    protected function lastRequestHasError(): bool
    {
        return $this->db->last_error !== null && $this->db->last_error !== '';
    }

    /**
     * @inheritDoc
     */
    public function raw($value): Expression
    {
        return new Expression($value);
    }

    /**
     * @param string $query
     * @param array $binding
     * @param \Closure $callback
     * @return mixed
     */
    protected function run(string $query, array $binding, \Closure $callback): mixed
    {
        $start = microtime(true);
        try {
            $result = $this->runQueryCallback(
                $query,
                $binding,
                $callback
            );
        } catch (QueryException $exception) {
            $result = $this->handleQueryException(
                $exception,
                $query,
                $binding,
                $callback
            );
        }

        $this->logQuery($query, $binding, $this->getElapsedTime($start));
        return $result;
    }

    /**
     * Run a SQL statement.
     *
     * @param string $query
     * @param array $bindings
     * @param \Closure $callback
     * @return mixed
     */
    protected function runQueryCallback(string $query, array $bindings, \Closure $callback): mixed
    {
        try {
            $result = $callback($query, $bindings);
            if ($result === false || $this->lastRequestHasError()) {
                throw new \Exception($this->db->last_error);
            }

            return $result;
        } catch (\Exception $exception) {
            throw new QueryException(
                $this->getName(),
                $query,
                $this->prepareBindings($bindings),
                $exception
            );
        }
    }

    /**
     * Handle a query exception.
     *
     * @param QueryException $exception
     * @param string $query
     * @param array $bindings
     * @param \Closure $callback
     * @return mixed
     */
    protected function handleQueryException(
        QueryException $exception,
        string $query,
        array $bindings,
        \Closure $callback
    ): mixed {
        if ($this->transactionCount >= 1) {
            throw $exception;
        }

        if ($this->causedByLostConnection($exception->getPrevious())) {
            if (!$this->db->db_connect()) {
                throw new LostConnectionException('Lost connection.');
            }

            return $this->runQueryCallback($query, $bindings, $callback);
        }

        throw $exception;
    }

    /**
     * Get the elapsed time since a given starting point.
     *
     * @param  float $start
     * @return float
     */
    protected function getElapsedTime(float $start): float
    {
        return round((microtime(true) - $start) * 1000, 2);
    }

    /**
     * Log a query in the connection's query log.
     *
     * @param string $query
     * @param array $bindings
     * @param float|null $queryDuration
     * @return void
     */
    public function logQuery(string $query, array $bindings, float $queryDuration = null): void
    {
        /**
         * If you want to log queries, you must enable the constant SAVEQUERIES
         * @see https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/#savequeries
         */
    }
}
