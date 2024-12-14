<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Orm;

use Dbout\WpOrm\Models\Post;
use Dbout\WpOrm\Orm\Database;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class DatabaseTest extends TestCase
{
    private Database $database;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->database = Database::getInstance();
    }

    /**
     * @return void
     * @covers Database::getTablePrefix
     */
    public function testGetTablePrefix(): void
    {
        global $wpdb;
        $this->assertEquals($wpdb->prefix, $this->database->getTablePrefix());
    }

    /**
     * @return void
     * @covers Database::getDatabaseName
     * @covers Database::getConfig
     */
    public function testGetDatabaseName(): void
    {
        $this->assertEquals('wordpress_test', $this->database->getDatabaseName());
    }

    /**
     * @return void
     * @covers Database::getName
     * @covers Database::getConfig
     */
    public function testGetName(): void
    {
        $this->assertEquals('wp-eloquent-mysql2', $this->database->getName());
    }

    /**
     * @param string $table
     * @param string|null $alias
     * @param string $expectedQuery
     * @return void
     * @covers Database::table
     * @dataProvider providerTestTable
     */
    public function testTable(string $table, ?string $alias, string $expectedQuery): void
    {
        $builder = $this->database->table($table, $alias);
        $this->assertEquals($expectedQuery, $builder->toSql());
    }

    /**
     * @return \Generator
     */
    protected function providerTestTable(): \Generator
    {
        yield 'Without alias' => [
            'options',
            null,
            sprintf('select * from `%s`', $this->getTable('options')),
        ];

        yield 'With alias' => [
            'options',
            'opts',
            sprintf('select * from `%s` as `opts`', $this->getTable('options')),
        ];
    }

    /**
     * @return void
     * @covers Database::lastInsertId
     */
    public function testLastInsertId(): void
    {
        $post = new Post();
        $post->setPostType('product');

        $post->save();
        $this->assertEquals(Database::getInstance()->lastInsertId(), $post->getId());
    }
}
