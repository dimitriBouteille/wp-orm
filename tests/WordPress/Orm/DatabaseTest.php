<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Orm;

use Dbout\WpOrm\Orm\Database;

/**
 * @coversDefaultClass \Dbout\WpOrm\Orm\Database
 */
class DatabaseTest extends \WP_UnitTestCase
{
    private Database $database;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->database = Database::getInstance();
    }

    /**
     * @return void
     * @covers ::getTablePrefix
     */
    public function testGetTablePrefix(): void
    {
        $this->assertEquals('wptests_', $this->database->getTablePrefix());
    }

    /**
     * @return void
     * @covers ::getDatabaseName
     */
    public function testGetDatabaseName(): void
    {
        $this->assertEquals('wptests_', $this->database->getDatabaseName());
    }
}
