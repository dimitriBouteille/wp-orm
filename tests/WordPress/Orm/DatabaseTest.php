<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Orm;

use Dbout\WpOrm\Orm\Database;

class DatabaseTest extends \WP_UnitTestCase
{
    private Database $database;

    public function set_up(): void
    {
        parent::set_up();
        $this->database = Database::getInstance();
    }

    /**
     * @return void
     * @covers \Dbout\WpOrm\Orm\Database::query
     */
    public function test_first()
    {
        global $wpdb;
        $result = $wpdb->get_results(sprintf('SELECT * FROM %soptions', $wpdb->prefix));
        var_dump($result);
        $this->assertEquals($wpdb->prefix, 'wptests_');
    }
}
