<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Dbout\WpOrm\Tests\WordPress\Orm\Schemas;

use Dbout\WpOrm\Orm\Database;
use Dbout\WpOrm\Orm\Schemas\WordPressBuilder;
use Dbout\WpOrm\Tests\WordPress\TestCase;
use Illuminate\Database\Schema\Blueprint;

class WordPressBuilderTest extends TestCase
{
    private Database $database;
    private WordPressBuilder $schema;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        $this->database = Database::getInstance();

        /** @var WordPressBuilder $schema */
        $schema  = $this->database->getSchemaBuilder();
        $this->schema = $schema;
    }

    /**
     * @return void
     * @covers WordPressBuilder::create
     * @covers WordPressBuilder::hasTable
     * @covers WordPressBuilder::getColumns
     * @covers WordPressBuilder::hasColumn
     */
    public function testCreate(): void
    {
        $tableName = 'architect';
        $this->schema->create($tableName, function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->json('data')->nullable();
        });

        $this->assertTrue($this->schema->hasTable($tableName));
        $columns = $this->schema->getColumns($tableName);

        $this->assertCount(4, $columns);
        $this->assertTrue($this->schema->hasColumn($tableName, 'id'));
        $this->assertTrue($this->schema->hasColumn($tableName, 'name'));
        $this->assertTrue($this->schema->hasColumn($tableName, 'slug'));
        $this->assertTrue($this->schema->hasColumn($tableName, 'data'));
    }

    /**
     * @return void
     * @covers WordPressBuilder::table
     * @covers WordPressBuilder::hasTable
     */
    public function testUpdate(): void
    {
        $tableName = 'projects';
        $this->schema->create($tableName, function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('author');
            $table->string('address');
        });

        $this->assertTrue($this->schema->hasTable($tableName));
        $this->schema->table($tableName, function (Blueprint $table) {
            $table->string('country');
            $table->boolean('finish');
        });

        $this->assertTrue($this->schema->hasColumn($tableName, 'country'));
        $this->assertTrue($this->schema->hasColumn($tableName, 'finish'));
    }

    /**
     * @return void
     * @covers WordPressBuilder::drop
     */
    public function testDrop(): void
    {
        $this->schema->create('company', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
        });

        $this->assertTrue($this->schema->hasTable('company'));
        $this->schema->drop('company');
        $this->assertFalse($this->schema->hasTable('company'));
    }

    /**
     * @return void
     * @covers WordPressBuilder::dropColumns
     */
    public function testDropColumn(): void
    {
        $tableName = 'address';
        $this->schema->create($tableName, function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('street_1');
            $table->string('street_2');
            $table->string('street_3');
        });

        $columns = $this->schema->getColumns($tableName);
        $this->assertCount(6, $columns);

        $this->schema->dropColumns('address', ['street_3']);
        $columns = $this->schema->getColumns($tableName);
        $this->assertCount(5, $columns);
        $this->assertFalse($this->schema->hasColumn($tableName, 'street_3'));

    }
}
