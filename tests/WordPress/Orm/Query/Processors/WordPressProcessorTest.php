<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Orm\Query\Processors;

use Dbout\WpOrm\Orm\Database;
use Dbout\WpOrm\Orm\Query\Processors\WordPressProcessor;
use Dbout\WpOrm\Tests\WordPress\Support\CreatesCustomTable;
use Dbout\WpOrm\Tests\WordPress\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

/**
 * Schema introspection results (columns, indexes, foreign keys) must be normalized
 * the same way as on a native Laravel MySQL connection.
 */
class WordPressProcessorTest extends TestCase
{
    use CreatesCustomTable;

    private const TABLE_NAME = 'processor_orders';

    private Builder $schema;

    /**
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::createCustomTable(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('status', 20)->default('pending')->index();
            $table->string('note')->nullable();
            $table->foreignId('post_id')->constrained('posts', 'ID')->cascadeOnDelete();
        });
    }

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->schema = Database::getInstance()->getSchemaBuilder();
    }

    /**
     * @return void
     * @covers WordPressProcessor::processColumns
     */
    public function testGetColumns(): void
    {
        $columns = array_column($this->schema->getColumns(self::TABLE_NAME), null, 'name');

        $this->assertEqualsCanonicalizing(['id', 'reference', 'status', 'note', 'post_id'], array_keys($columns));

        $this->assertTrue($columns['id']['auto_increment']);
        $this->assertFalse($columns['id']['nullable']);
        $this->assertSame('bigint', $columns['id']['type_name']);

        $this->assertTrue($columns['note']['nullable']);
        $this->assertFalse($columns['note']['auto_increment']);

        $this->assertSame('varchar(20)', $columns['status']['type']);
        $this->assertStringContainsString('pending', (string) $columns['status']['default']);
    }

    /**
     * @return void
     * @covers WordPressProcessor::processColumnListing
     */
    public function testGetColumnListing(): void
    {
        $this->assertEqualsCanonicalizing(
            ['id', 'reference', 'status', 'note', 'post_id'],
            $this->schema->getColumnListing(self::TABLE_NAME)
        );
    }

    /**
     * @return void
     * @covers WordPressProcessor::processIndexes
     */
    public function testGetIndexes(): void
    {
        $indexes = array_column($this->schema->getIndexes(self::TABLE_NAME), null, 'name');

        $this->assertTrue($indexes['primary']['primary']);
        $this->assertTrue($indexes['primary']['unique']);
        $this->assertSame(['id'], $indexes['primary']['columns']);

        $this->assertTrue($indexes['processor_orders_reference_unique']['unique']);
        $this->assertFalse($indexes['processor_orders_reference_unique']['primary']);
        $this->assertSame(['reference'], $indexes['processor_orders_reference_unique']['columns']);

        $this->assertFalse($indexes['processor_orders_status_index']['unique']);
        $this->assertSame(['status'], $indexes['processor_orders_status_index']['columns']);
    }

    /**
     * @return void
     * @covers WordPressProcessor::processIndexes
     */
    public function testHasIndex(): void
    {
        $this->assertTrue($this->schema->hasIndex(self::TABLE_NAME, ['status']));
        $this->assertTrue($this->schema->hasIndex(self::TABLE_NAME, ['reference'], 'unique'));
        $this->assertTrue($this->schema->hasIndex(self::TABLE_NAME, 'processor_orders_status_index'));
        $this->assertFalse($this->schema->hasIndex(self::TABLE_NAME, ['note']));
    }

    /**
     * @return void
     * @covers WordPressProcessor::processForeignKeys
     */
    public function testGetForeignKeys(): void
    {
        global $wpdb;
        $foreignKeys = $this->schema->getForeignKeys(self::TABLE_NAME);

        $this->assertCount(1, $foreignKeys);
        $this->assertSame('processor_orders_post_id_foreign', $foreignKeys[0]['name']);
        $this->assertSame(['post_id'], $foreignKeys[0]['columns']);
        $this->assertSame($wpdb->posts, $foreignKeys[0]['foreign_table']);
        $this->assertSame(['ID'], $foreignKeys[0]['foreign_columns']);
        $this->assertSame('cascade', $foreignKeys[0]['on_delete']);
        $this->assertSame('no action', $foreignKeys[0]['on_update']);
    }
}
