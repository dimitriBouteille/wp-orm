<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests;

trait WpDatabaseInstanceCreator
{
    /**
     * @return void
     */
    protected function initWpDatabaseInstance(): void
    {
        $GLOBALS['wpdb'] = new \wpdb('db_user', 'db_password', 'test_database', '127.0.0.0');
    }
}
