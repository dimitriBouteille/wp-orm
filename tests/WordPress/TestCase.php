<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress;

abstract class TestCase extends \WP_UnitTestCase
{
    /**
     * @param string $query
     * @return void
     */
    public static function assertLastQueryEqual(string $query): void
    {
        global $wpdb;
        self::assertEquals($query, $wpdb->last_query);
    }

    /**
     * @param string $table
     * @return string
     */
    protected function getTable(string $table): string
    {
        global $wpdb;
        return $wpdb->prefix . $table;
    }
}
