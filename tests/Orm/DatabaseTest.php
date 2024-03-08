<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\Orm;

use Dbout\WpOrm\Orm\Database;
use Dbout\WpOrm\Tests\WpDatabaseInstanceCreator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Dbout\WpOrm\Orm\Database
 */

class DatabaseTest extends TestCase
{
    use WpDatabaseInstanceCreator;

    /**
     * @return void
     * @covers ::getInstance
     */
    public function testInvalidWPInstance(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The global variable $wpdb must be instance of \wpdb.');
        Database::getInstance();
    }
}
