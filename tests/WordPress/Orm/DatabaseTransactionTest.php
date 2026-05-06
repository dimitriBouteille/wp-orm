<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Orm;

use Dbout\WpOrm\Orm\AbstractModel;
use Dbout\WpOrm\Orm\Database;
use Dbout\WpOrm\Tests\WordPress\Support\CreatesCustomTable;
use Dbout\WpOrm\Tests\WordPress\TestCase;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;

class DatabaseTransactionTest extends TestCase
{
    use CreatesCustomTable;

    private string $tableName = '';
    private AbstractModel $model;
    private Database $db;

    /**
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::createCustomTable('document', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('url', 55)->default('');
        });
    }

    /**
     * @return void
     */
    public function setUp(): void
    {
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
        $this->resetLogQueries();
        $this->db->transaction(function () {
            $query = sprintf('INSERT INTO %s (name, url) VALUES(?, ?);', $this->tableName);
            $this->db->insert($query, ['Invoice #15', 'invoice-15']);
            $this->db->insert($query, ['Invoice #16', 'invoice-16']);
        });

        $this->assertTransaction('commit');
        $this->assertCount(2, $this->model::all()->toArray());
    }

    /**
     * @throws \Throwable
     * @return void
     * @covers Database::transaction
     * @covers Database::delete
     * @covers Database::insert
     * @covers Database::rollBack
     */
    public function testTransactionRollback(): void
    {
        $query = sprintf('INSERT INTO %s (name, url) VALUES(?, ?);', $this->tableName);
        $this->db->insert($query, ['Deposit #1', 'deposit-1']);
        $this->db->insert($query, ['Deposit #2', 'deposit-2']);

        $this->resetLogQueries();
        try {
            $this->db->transaction(function () use ($query) {
                $this->db->insert($query, ['Deposit #99', 'deposit-99']);
                $this->db->delete(sprintf('DELETE FROM %s;', $this->tableName));

                /**
                 * Throw exception because fake_column is invalid column name.
                 */
                $this->db->delete(sprintf('DELETE FROM %s WHERE fake_column = %d;', $this->tableName, $query));
            });
        } catch (\Exception) {
            // Off exception
        }

        $this->assertTransaction('rollback');

        $items = $this->model::all();
        $this->assertCount(2, $items->toArray(), 'There must be only 2 items because the transaction was rollback.');
        $this->assertEquals(['deposit-1', 'deposit-2'], $items->pluck('url')->toArray());
    }

    /**
     * @throws \Throwable
     * @return void
     * @covers Database::transaction
     */
    public function testTransactionThrowsQueryException(): void
    {
        $this->expectException(QueryException::class);
        $this->resetLogQueries();
        $this->db->transaction(function () {
            $this->db->delete('DELETE FROM fake_table;');
        });

        $this->assertTransaction('rollback');
    }

    /**
     * @throws \Throwable
     * @return void
     * @covers Database::beginTransaction
     */
    public function testBeginTransaction(): void
    {
        $startLevel = $this->db->transactionLevel();
        $this->db->beginTransaction();
        $this->assertSame($startLevel + 1, $this->db->transactionLevel());

        // Clean up so the next test does not inherit an open transaction.
        $this->db->rollBack();
    }

    /**
     * @throws \Throwable
     * @return void
     * @covers Database::rollBack
     */
    public function testRollback(): void
    {
        $startLevel = $this->db->transactionLevel();
        $this->db->beginTransaction();
        $this->db->rollBack();
        $this->assertSame($startLevel, $this->db->transactionLevel());
    }

    /**
     * @throws \Throwable
     * @return void
     * @covers Database::commit
     */
    public function testCommit(): void
    {
        $startLevel = $this->db->transactionLevel();
        $this->db->beginTransaction();
        $this->db->commit();
        $this->assertSame($startLevel, $this->db->transactionLevel());
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
        $this->assertEquals(0, $this->db->transactionLevel());

        if ($mode === 'commit') {
            $this->assertEquals('COMMIT;', $lastQuery);
        } elseif ($mode === 'rollback') {
            $this->assertEquals('ROLLBACK;', $lastQuery);
        }
    }

    /**
     * @return void
     */
    private function resetLogQueries(): void
    {
        global $wpdb;
        $wpdb->queries = [];
    }
}
