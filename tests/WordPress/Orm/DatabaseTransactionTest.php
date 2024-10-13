<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Orm;

use Dbout\WpOrm\Orm\AbstractModel;
use Dbout\WpOrm\Orm\Database;
use Dbout\WpOrm\Tests\WordPress\TestCase;
use Illuminate\Database\QueryException;

class DatabaseTransactionTest extends TestCase
{
    private string $tableName = '';
    private AbstractModel $model;
    private Database $db;

    /**
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        global $wpdb;

        $tableName = $wpdb->prefix . 'document';
        $sql = "CREATE TABLE $tableName (
            id INT NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            url varchar(55) DEFAULT '' NOT NULL,
            PRIMARY KEY  (id)
        );";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /**
     * @return void
     */
    public function setUp(): void
    {
        define('SAVEQUERIES', false);
        $this->model = new class () extends AbstractModel {
            protected $primaryKey = 'id';
            public $timestamps = false;
            protected $table = 'document';
        };

        global $wpdb;
        $this->tableName = $wpdb->prefix . 'document';
        $this->model::truncate();
        $this->db = Database::getInstance();
    }

    /**
     * @throws \Throwable
     * @return void
     * @covers Database::transaction
     * @covers Database::insert
     * @covers Database::commit
     */
    public function testTransactionCommit(): void
    {
        $this->activeLogQueries();
        $this->db->transaction(function () {
            $query = sprintf('INSERT INTO %s (name, url) VALUES(?, ?);', $this->tableName);
            $this->db->insert($query, ['Invoice #15', 'invoice-15']);
            $this->db->insert($query, ['Invoice #16', 'invoice-16']);
        });

        $this->assertTransaction('commit');
        $this->assertCount(2, $this->model::all()->toArray());
    }

    /**
     * @return void
     * @covers Database::transaction
     * @covers Database::delete
     * @covers Database::insert
     * @covers Database::rollBack
     * @throws \Throwable
     */
    public function testTransactionRollback(): void
    {
        $query = sprintf('INSERT INTO %s (name, url) VALUES(?, ?);', $this->tableName);
        $this->db->insert($query, ['Deposit #1', 'deposit-1']);
        $this->db->insert($query, ['Deposit #2', 'deposit-2']);

        $this->activeLogQueries();
        try {
            $this->db->transaction(function () use ($query) {
                $this->db->insert($query, ['Deposit #99', 'deposit-99']);
                $this->db->delete(sprintf('DELETE FROM %s;', $this->tableName));

                /**
                 * Throw exception because fake_column is invalid column name.
                 */
                $this->db->delete(sprintf('DELETE FROM %s WHERE fake_column = %d;', $this->tableName, $query));
            });
        } catch (\Exception $exception) {
            // Off exception
            $this->assertInstanceOf(QueryException::class, $exception);
        }

        $this->assertTransaction('rollback');

        $items = $this->model::all();
        $this->assertCount(2, $items->toArray(), 'There must be only 2 items because the transaction was rollback.');
    }

    /**
     * @param string $mode
     * @return void
     */
    private function assertTransaction(string $mode): void
    {
        global $wpdb;
        $query = $wpdb->queries;

        $firstQuery = reset($query)[0] ?? '';
        $lastQuery = end($query)[0] ?? '';
        $this->assertEquals('START TRANSACTION;', $firstQuery);

        if ($mode === 'commit') {
            $this->assertEquals('COMMIT;', $lastQuery);
        } elseif ($mode === 'rollback') {
            $this->assertEquals('ROLLBACK;', $lastQuery);
        }
    }

    /**
     * @return void
     */
    private function activeLogQueries(): void
    {
        define('SAVEQUERIES', true);
    }
}
