<?php

namespace Dbout\WpOrm\Tests;

trait WpDatabaseInstanceCreator
{
    /**
     * @return void
     */
    protected function initWpDatabaseInstance(): void
    {
        $instance = new \wpdb('db_user', 'db_password', 'test_database', '127.0.0.0');
        $GLOBALS['wpdb'] = $instance;
    }
}
