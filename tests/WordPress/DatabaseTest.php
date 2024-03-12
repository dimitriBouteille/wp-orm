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
        $this->assertEquals($wpdb->db_version(), 5.5);
        $this->assertEquals($wpdb->prefix, 'test');
    }
}
