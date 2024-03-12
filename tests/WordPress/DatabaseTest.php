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
    public function set_up()
    {
        global $wpdb;
        var_dump($wpdb);
    }
}
