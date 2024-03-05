<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Orm;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;

class Database implements ConnectionInterface
{
    /**
     * @var bool
     */
    public $loggingQueries;

    /**
     * @var \wpdb
     */
    public $db;

    /**
     * Count of active transactions
     * @var int
     */
    public int $transactionCount = 0;

    /**
     * The database connection configuration options.
     * @var array
     */
    protected array $config = [
        'name' => 'wp-eloquent-mysql2',
    ];

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
     * Database constructor.
     */
    public function __construct()
    {
        global $wpdb;
        /**
         * @todo Check $wpdb instance
         */

        if ($wpdb instanceof \wpdb) {
            $this->tablePrefix = $wpdb->prefix;
        }

        $this->db = $wpdb;
    }

    /**
     * @inheritDoc
     */
    public function getDatabaseName()
    {
        return $this->getConfig('name');
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return $this->getDatabaseName();
    }

    /**
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
        $processor = $this->getPostProcessor();
        $table = $this->getTablePrefix() . $table;
        $query = new Builder($this, $this->getQueryGrammar(), $processor);

        return $query->from($table);
    }

    /**
     * @inheritDoc
     */
    public function raw($value): Expression
    {
        return new Expression($value);
    }

    /**
     * Get a new query builder instance.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function query()
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
        $query = $this->bind_params($query, $bindings);
        $result = $this->db->get_row($query);
        if ($result === false || $this->db->last_error) {
            throw new QueryException($this->getName(), $query, $bindings, new \Exception($this->db->last_error));
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function select($query, $bindings = [], $useReadPdo = true): array
    {
        $query = $this->bind_params($query, $bindings);
        $result = $this->db->get_results($query);
        if ($result === false || $this->lastRequestHasError()) {
            throw new QueryException($this->getName(), $query, $bindings, new \Exception($this->db->last_error));
        }

        return $result;
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
     * Bind and run the query
     *
     * @param  string $query
     * @param  array $bindings
     * @throws QueryException
     *
     * @return array
     */
    public function bind_and_run($query, $bindings = [])
    {
        $new_query = $this->bind_params($query, $bindings);
        $result = $this->db->query($new_query);
        if ($result === false || $this->db->last_error) {
            throw new QueryException($new_query, $bindings, new \Exception($this->db->last_error));
        }

        return (array) $result;
    }

    /**
     * @inheritDoc
     */
    public function insert($query, $bindings = []): bool
    {
        $newQuery = $this->bind_params($query, $bindings, true);
        $result = $this->unprepared($newQuery);
        if ($result === false || $this->lastRequestHasError()) {
            throw new QueryException($this->getName(), $newQuery, $bindings, new \Exception($this->db->last_error));
        }

        return $result;
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
        $newQuery = $this->bind_params($query, $bindings, true);
        return $this->unprepared($newQuery);
    }

    /**
     * @inheritDoc
     */
    public function affectingStatement($query, $bindings = []): int
    {
        $newQuery = $this->bind_params($query, $bindings, true);
        $result = $this->db->query($newQuery);

        if ($result === false || $this->lastRequestHasError()) {
            throw new QueryException($this->getName(), $newQuery, $bindings, new \Exception($this->db->last_error));
        }

        return (int) $result;
    }

    /**
     * @inheritDoc
     */
    public function unprepared($query): bool
    {
        /**
         * @see \wpdb::print_error()
         */
        $result = $this->db->query($query);
        return ($result === false || $this->db->last_error);
    }

    /**
     * @inheritDoc
     */
    public function prepareBindings(array $bindings): array
    {
        $grammar = $this->getQueryGrammar();
        foreach ($bindings as $key => $value) {
            if (\is_bool($value)) {
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

    public function getPostProcessor()
    {
        return new Processor();
    }

    public function getQueryGrammar()
    {
        return new Grammar();
    }

    /**
     * Return the last insert id
     *
     * @param $args
     * @return int
     */
    public function lastInsertId($args)
    {
        return $this->db->insert_id;
    }

    /**
     * Get an option from the configuration options.
     *
     * @param  string|null  $option
     * @return mixed
     */
    public function getConfig($option = null)
    {
        return Arr::get($this->config, $option);
    }

    protected function exception($exception)
    {
    }

    /**
     * @return $this
     */
    public function getPdo()
    {
        return $this;
    }

    /**
     * Enable the query log on the connection.
     *
     * @return void
     */
    public function enableQueryLog()
    {
        $this->loggingQueries = true;
    }

    /**
     * @return bool
     */
    protected function lastRequestHasError(): bool
    {
        return $this->db->last_error !== null && $this->db->last_error !== '';
    }
}
