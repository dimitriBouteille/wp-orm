<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Migrations;

use Dbout\WpOrm\Orm\Database;
use Dbout\WpOrm\Orm\Schemas\WordPressBuilder;

/**
 * Base class of the migration files.
 *
 * Table names are given without the WordPress prefix, it is added automatically.
 * MySQL does not support transactional DDL: a migration that fails halfway is not rolled back.
 */
abstract class Migration extends \Illuminate\Database\Migrations\Migration
{
    /**
     * @return WordPressBuilder
     */
    protected function schema(): WordPressBuilder
    {
        return Database::getInstance()->getSchemaBuilder();
    }
}
