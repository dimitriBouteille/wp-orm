<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Orm;

use Dbout\WpOrm\Exceptions\WpOrmException;
use Dbout\WpOrm\Orm\Query\Grammars\WordPressGrammar;
use Dbout\WpOrm\Orm\Query\Processors\WordPressProcessor;
use Dbout\WpOrm\Orm\Schemas\WordPressBuilder;
use Illuminate\Database\Connection;
use Illuminate\Database\LostConnectionException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Database\Schema\Grammars\MySqlGrammar as SchemaGrammar;

/**
 * @see https://developer.wordpress.org/reference/classes/wpdb/
 */
class Database extends Connection
{
    /**
     * @var \wpdb
     */
    protected \wpdb $db;

    /**
     * Count of active transactions.
     * @var int
     */
    public int $transactionCount = 0;

    /**
     * @var Database|null
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

        $pdo = function (): never {
            throw new \Exception('PDO property can\'t be used.');
        };

        parent::__construct(
            $pdo,
            defined('DB_NAME') ? DB_NAME : '',
            $wpdb->prefix,
            [
                'name' => 'wp-eloquent-mysql2',
                'charset' => $wpdb->charset,
                'collate' => $wpdb->collate,
                'version' => $wpdb->db_version(),
            ]
        );

        $this->db = $wpdb;
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
            $query = $this->bindParams($query, $bindings);
            return $this->db->get_row($query);
        });
    }

    /**
     * @inheritDoc
     */
    public function select($query, $bindings = [], $useReadPdo = true): array
    {
        return $this->run($query, $bindings, function (string $query, array $bindings) {
            $query = $this->bindParams($query, $bindings);
            return $this->db->get_results($query);
        });
    }

    /**
     * @inheritDoc
     */
    public function cursor($query, $bindings = [], $useReadPdo = true): \Generator
    {
        $results = $this->select($query, $bindings, $useReadPdo);
        foreach ($results as $result) {
            yield $result;
        }
    }

    /**
     * A hacky way to emulate bind parameters into SQL query.
     *
     * @param string|null $query
     * @param array $bindings
     * @return string
     */
    private function bindParams(?string $query, array $bindings): string
    {
        $query = \str_replace('"', '`', (string)$query);
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
            $query = $this->bindParams($query, $bindings);
            return $this->db->query($query);
        });
    }

    /**
     * @inheritDoc
     */
    public function affectingStatement($query, $bindings = []): int
    {
        return $this->run($query, $bindings, function (string $query, array $bindings) {
            $newQuery = $this->bindParams($query, $bindings);
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
            $result = $this->db->query($query);
            return $this->lastRequestHasError() ? $result : true;
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

            // We'll simply execute the given callback within a try / catch block and if we
            // catch any exception we can rollback this transaction so that none of this
            // gets actually persisted to a database or stored in a permanent fashion.
            $data = $callback($this);
            $this->commit();
            return $data;
        } catch (\Exception $e) {

            // If we catch an exception we'll rollback this transaction
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     */
    public function beginTransaction(): void
    {
        $transaction = $this->unprepared('START TRANSACTION;');
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

        $transaction = $this->unprepared('COMMIT;');
        if ($transaction) {
            $this->transactionCount--;
        }
    }

    /**
     * @inheritDoc
     */
    public function rollBack($toLevel = null): void
    {
        if ($this->transactionCount < 1) {
            return;
        }

        $transaction = $this->unprepared('ROLLBACK;');
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
     * @throws WpOrmException
     * @see https://laravel.com/docs/10.x/eloquent#pruning-models
     */
    public function pretend(\Closure $callback): array
    {
        throw new WpOrmException('pretend feature not supported.');
    }

    /**
     * Return the last insert id.
     *
     * @return int|null
     */
    public function lastInsertId(): ?int
    {
        return $this->db->insert_id;
    }

    /**
     * Returns true if the last query has error.
     *
     * @return bool
     * @see \wpdb::print_error()
     */
    public function lastRequestHasError(): bool
    {
        return $this->db->last_error !== null && $this->db->last_error !== '';
    }

    /**
     * @inheritDoc
     */
    protected function run($query, $bindings, \Closure $callback): mixed
    {
        $start = microtime(true);
        try {
            $result = $this->runQueryCallback(
                $query,
                $bindings,
                $callback
            );
        } catch (QueryException $exception) {
            $result = $this->handleQueryException(
                $exception,
                $query,
                $bindings,
                $callback
            );
        }

        $this->logQuery($query, $bindings, $this->getElapsedTime((int)$start));
        return $result;
    }

    /**
     * @inheritDoc
     */
    protected function runQueryCallback($query, $bindings, \Closure $callback): mixed
    {
        try {
            // Disable display WP error and save previous state
            $suppressionError = $this->db->suppress_errors();

            $result = $callback($query, $bindings);

            // Restore the state
            $this->db->suppress_errors($suppressionError);

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
     * @param QueryException $e
     * @param string $query
     * @param array $bindings
     * @param \Closure $callback
     * @return mixed
     */
    protected function handleQueryException(
        QueryException $e,
        $query,
        $bindings,
        \Closure $callback
    ): mixed {
        if ($this->transactionCount >= 1) {
            throw $e;
        }

        if ($this->causedByLostConnection($e->getPrevious())) {
            if (!$this->db->db_connect()) {
                throw new LostConnectionException('Lost connection.');
            }

            return $this->runQueryCallback($query, $bindings, $callback);
        }

        throw $e;
    }

    /**
     * @inheritDoc
     * @see \wpdb::log_query
     */
    public function logQuery($query, $bindings, $time = null): void
    {
        /**
         * If you want to log queries, you must enable the constant SAVEQUERIES
         * @see https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/#savequeries
         */
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultSchemaGrammar(): Grammar
    {
        ($grammar = new SchemaGrammar())->setConnection($this);

        /** @var Grammar $grammar */
        $grammar = $this->withTablePrefix($grammar);
        return $grammar;
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultPostProcessor(): WordPressProcessor
    {
        return new WordPressProcessor();
    }

    /**
     * @inheritDoc
     */
    public function getSchemaBuilder(): \Illuminate\Database\Schema\Builder
    {
        // @phpstan-ignore-next-line
        if (!$this->schemaGrammar instanceof Grammar) {
            $this->useDefaultSchemaGrammar();
        }

        return new WordPressBuilder($this);
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultQueryGrammar(): WordPressGrammar
    {
        ($grammar = new WordPressGrammar())->setConnection($this);

        return $grammar;
    }
}
