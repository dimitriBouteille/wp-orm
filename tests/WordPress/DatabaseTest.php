<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress;

class DatabaseTest extends \WP_UnitTestCase
{
    /**
     * @return void
     * @covers \Dbout\WpOrm\Orm\Database::query
     */
    public function test_first()
    {
        global $wpdb;
        $result = $wpdb->get_results('SELECT * FROM options');
        $this->assertEmpty($result, 'The result should be empty.');
    }
}
