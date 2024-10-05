<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Orm;

use Dbout\WpOrm\Models\Post;
use Dbout\WpOrm\Orm\Database;
use Dbout\WpOrm\Tests\WordPress\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(Database::class)]
#[CoversFunction('getTablePrefix')]
#[CoversFunction('getDatabaseName')]
#[CoversFunction('getConfig')]
#[CoversFunction('getName')]
#[CoversFunction('table')]
#[CoversFunction('lastInsertId')]
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
     */
    public function testGetTablePrefix(): void
    {
        global $wpdb;
        $this->assertEquals($wpdb->prefix, $this->database->getTablePrefix());
    }

    /**
     * @return void
     */
    public function testGetDatabaseName(): void
    {
        $this->assertEquals('wordpress_test', $this->database->getDatabaseName());
    }

    /**
     * @return void
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
     */
    #[DataProvider('providerTestTable')]
    public function testTable(string $table, ?string $alias, string $expectedQuery): void
    {
        $builder = $this->database->table($table, $alias);
        $this->assertEquals($expectedQuery, $builder->toSql());
    }

    /**
     * @return \Generator
     */
    public static function providerTestTable(): \Generator
    {
        yield 'Without alias' => [
            'options',
            null,
            sprintf('select * from "%s"', self::getTable('options')),
        ];

        yield 'With alias' => [
            'options',
            'opts',
            sprintf('select * from "%s" as "opts"', self::getTable('options')),
        ];
    }

    /**
     * @return void
     */
    public function testLastInsertId(): void
    {
        $post = new Post();
        $post->setPostType('product');

        $post->save();
        $this->assertEquals(Database::getInstance()->lastInsertId(), $post->getId());
    }
}
