<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Support;

use Dbout\WpOrm\Orm\Database;
use Illuminate\Database\Schema\Blueprint;

/**
 * Helper for tests that need a custom database table for the lifetime of the test class.
 *
 * Tables registered via createCustomTable() are dropped automatically in
 * tearDownAfterClass(). This avoids the leak that occurs when tests call
 * dbDelta() or Schema::create() without ever cleaning up afterwards.
 *
 * Usage:
 *
 *     class FooTest extends TestCase
 *     {
 *         use CreatesCustomTable;
 *
 *         public static function setUpBeforeClass(): void
 *         {
 *             parent::setUpBeforeClass();
 *             self::createCustomTable('document', function (Blueprint $table) {
 *                 $table->id();
 *                 $table->string('name', 100);
 *             });
 *         }
 *     }
 */
trait CreatesCustomTable
{
    /**
     * Tables created by createCustomTable() during setUpBeforeClass.
     *
     * @var array<string>
     */
    private static array $customTables = [];

    /**
     * Create a custom table that is dropped automatically in tearDownAfterClass().
     *
     * The connection prefix is applied automatically — pass the table name without it.
     *
     * @param string $name
     * @param \Closure(Blueprint): void $blueprint
     * @return void
     */
    protected static function createCustomTable(string $name, \Closure $blueprint): void
    {
        $schema = Database::getInstance()->getSchemaBuilder();

        // Drop a leftover table from a previously interrupted run before recreating.
        $schema->dropIfExists($name);
        $schema->create($name, $blueprint);

        self::$customTables[] = $name;
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        $schema = Database::getInstance()->getSchemaBuilder();
        foreach (self::$customTables as $name) {
            $schema->dropIfExists($name);
        }

        self::$customTables = [];

        parent::tearDownAfterClass();
    }
}
