<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\Unit\Orm;

use Dbout\WpOrm\Orm\Database;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Dbout\WpOrm\Orm\Database
 */

class DatabaseTest extends TestCase
{
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
