<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\Orm;

use Dbout\WpOrm\Orm\Database;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Dbout\WpOrm\Orm\Database
 */
class DatabaseTest extends TestCase
{
    protected function setUp(): void
    {
        $instance = new \wpdb('db_user', 'db_password', 'test_database', '127.0.0.0');
        $GLOBALS['wpdb'] = $instance;
    }

    /**
     * @return void
     * @covers ::getInstance
     */
    public function testInvalidWPInstance(): void
    {
        $GLOBALS['wpdb'] = null;
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The global variable $wpdb must be instance of \wpdb.');
        Database::getInstance();
    }

    /**
     * @return void
     * @covers ::getDatabaseName
     */
    public function testConfig(): void
    {
    }
}
