<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
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

        if ($wpdb) {
            $this->tablePrefix = $wpdb->prefix;
        }

        if (!$this->tablePrefix && defined('DB_PREFIX')) {
            $this->tablePrefix = DB_PREFIX;
        }

        $this->db = $wpdb;
    }

    /**
     * @return mixed|string
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
     * @param \Closure|\Illuminate\Database\Query\Builder|string $table
     * @param null $as
     * @return Builder|\Illuminate\Database\Query\Builder
     */
    public function table($table, $as = null)
    {
        $processor = $this->getPostProcessor();
        $table = $this->getTablePrefix() . $table;
        $query = new Builder($this, $this->getQueryGrammar(), $processor);

        return $query->from($table);
    }

    /**
     * Get a new raw query expression.
     *
     * @param  mixed $value
     *
     * @return \Illuminate\Database\Query\Expression
     */
    public function raw($value)
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
     * Run a select statement and return a single result
     *
     * @param string $query
     * @param array $bindings
     * @param bool $useReadPdo
     * @return array|mixed|object|void|null
     */
    public function selectOne($query, $bindings = [], $useReadPdo = true)
    {
        $query = $this->bind_params($query, $bindings);
        $result = $this->db->get_row($query);
        if ($result === false || $this->db->last_error) {
            throw new QueryException($query, $bindings, new \Exception($this->db->last_error));
        }

        return $result;
    }

    /**
     * Run a select statement
     *
     * @param string $query
     * @param array $bindings
     * @param bool $useReadPdo
     * @return array|object|null
     */
    public function select($query, $bindings = [], $useReadPdo = true)
    {
        $query = $this->bind_params($query, $bindings);
        $result = $this->db->get_results($query);
        if ($result === false || $this->db->last_error) {
            throw new QueryException($query, $bindings, new \Exception($this->db->last_error));
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
     * @param string $query
     * @param array $bindings
     * @return bool
     */
    public function insert($query, $bindings = [])
    {
        return $this->statement($query, $bindings);
    }

    /**
     * @param string $query
     * @param array $bindings
     * @return int
     */
    public function update($query, $bindings = [])
    {
        return $this->affectingStatement($query, $bindings);
    }

    /**
     * @param string $query
     * @param array $bindings
     * @return int
     */
    public function delete($query, $bindings = [])
    {
        return $this->affectingStatement($query, $bindings);
    }

    /**
     * @param string $query
     * @param array $bindings
     * @return bool
     */
    public function statement($query, $bindings = [])
    {
        $newQuery = $this->bind_params($query, $bindings, true);
        return $this->unprepared($newQuery);
    }

    /**
     * @param string $query
     * @param array $bindings
     * @return int
     */
    public function affectingStatement($query, $bindings = [])
    {
        $new_query = $this->bind_params($query, $bindings, true);
        $result = $this->db->query($new_query);

        if ($result === false || $this->db->last_error) {
            throw new QueryException($new_query, $bindings, new \Exception($this->db->last_error));
        }

        return (int) $result;
    }

    /**
     * @param string $query
     * @return bool
     */
    public function unprepared($query)
    {
        $result = $this->db->query($query);
        return ($result === false || $this->db->last_error);
    }

    /**
     * @param array $bindings
     * @return array
     */
    public function prepareBindings(array $bindings)
    {
        $grammar = $this->getQueryGrammar();
        foreach ($bindings as $key => $value) {

            // Micro-optimization: check for scalar values before instances
            if (\is_bool($value)) {
                $bindings[$key] = (int) $value;
            } elseif (is_scalar($value)) {
                continue;
            } elseif ($value instanceof \DateTime) {
                // We need to transform all instances of the DateTime class into an actual
                // date string. Each query grammar maintains its own date string format
                // so we'll just ask the grammar for the format to get from the date.
                $bindings[$key] = $value->format($grammar->getDateFormat());
            }
        }

        return $bindings;
    }

    /**
     * @param \Closure $callback
     * @param int $attempts
     * @throws \Exception
     * @return mixed
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
     * Start a new database transaction.
     *
     * @return void
     */
    public function beginTransaction()
    {
        $transaction = $this->unprepared("START TRANSACTION;");
        if ($transaction) {
            $this->transactionCount++;
        }
    }

    /**
     * Commit the active database transaction.
     *
     * @return void
     */
    public function commit()
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
     * Rollback the active database transaction.
     *
     * @return void
     */
    public function rollBack()
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
     * Get the number of active transactions.
     *
     * @return int
     */
    public function transactionLevel()
    {
        return $this->transactionCount;
    }

    /**
     * Execute the given callback in "dry run" mode.
     *
     * @param  \Closure $callback
     *
     * @return array
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
}
