<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Helpers;

use Dbout\WpOrm\Orm\AbstractModel;

trait WithFindOneBy
{
    /**
     * @param string $table
     * @param string $whereColumn
     * @param string $whereValue
     * @return void
     */
    protected function checkFindOneByQuery(string $table, string $whereColumn, string $whereValue): void
    {
        global $wpdb;
        $table = $wpdb->prefix . $table;

        $this->assertEquals(
            sprintf(
                "select `%s`.* from `%s` where `%s` = '%s' limit 1",
                $table,
                $table,
                $whereColumn,
                $whereValue
            ),
            $wpdb->last_query
        );
    }

    /**
     * @param AbstractModel|null $model
     * @param string $expectedClass
     * @return void
     */
    protected function checkFindOneByModel(?AbstractModel $model, string $expectedClass): void
    {
        $this->assertInstanceOf($expectedClass, $model);
    }
}
