<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

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
     * @return void
     */
    public function setUp(): void
    {
        $this->database = Database::getInstance();

        /** @var WordPressBuilder $schema */
        $schema  = $this->database->getSchemaBuilder();
        $this->schema = $schema;

        $this->schema->create('project', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('author');
            $table->string('address');
        });
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
        $this->schema->create('architect', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->json('data')->nullable();
        });

        $this->assertTrue($this->schema->hasTable('architect'));
        $columns = $this->schema->getColumns('architect');
        $table = $this->database->getTablePrefix() . 'architect';
        $this->assertCount(4, $columns);
        $this->assertTrue($this->schema->hasColumn($table, 'id'));
        $this->assertTrue($this->schema->hasColumn($table, 'name'));
        $this->assertTrue($this->schema->hasColumn($table, 'slug'));
        $this->assertTrue($this->schema->hasColumn($table, 'data'));
    }

    /**
     * @return void
     * @covers WordPressBuilder::table
     * @covers WordPressBuilder::hasTable
     */
    public function testUpdate(): void
    {
        $this->schema->table('project', function (Blueprint $table) {
            $table->string('country');
            $table->boolean('finish');
        });

        $table = $this->database->getTablePrefix() . 'project';
        $this->assertTrue($this->schema->hasColumn($table, 'country'));
        $this->assertTrue($this->schema->hasColumn($table, 'finish'));
    }
}
