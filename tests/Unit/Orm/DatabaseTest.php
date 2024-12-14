<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\Unit\Orm;

use Dbout\WpOrm\Orm\Database;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

#[CoversClass(Database::class)]
#[CoversFunction('getInstance')]
class DatabaseTest extends TestCase
{
    /**
     * @return void
     */
    public function testInvalidWPInstance(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The global variable $wpdb must be instance of \wpdb.');
        Database::getInstance();
    }
}
