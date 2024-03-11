<?php

namespace Dbout\WpOrm\Tests\WordPress;

class DatabaseTest extends \WP_UnitTestCase
{
    public function set_up()
    {
        global $wpdb;
        var_dump($wpdb);
    }
}
