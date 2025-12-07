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
     * @inheritDoc
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
     * @covers Database::table
     * @return void
     */
    public function testGetTableWithoutAlias(): void
    {
        $builder = $this->database->table('options');
        $this->assertEquals(
            sprintf('select * from `%s`', $this->getTable('options')),
            $builder->toSql()
        );
    }

    /**
     * @covers Database::table
     * @return void
     */
    public function testGetTableWithAlias(): void
    {
        $builder = $this->database->table('options', 'opts');
        $this->assertEquals(
            sprintf(
                'select * from `%s` as `%s`',
                $this->getTable('options'),
                $this->getTable('opts')
            ),
            $builder->toSql()
        );
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
