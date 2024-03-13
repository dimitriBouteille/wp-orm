<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Helpers;

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
        $table = $this->getTable($table);
        $this->assertLastQueryEqual(
            sprintf(
                "select `%s`.* from `%s` where `%s` = '%s' limit 1",
                $table,
                $table,
                $whereColumn,
                $whereValue
            )
        );
    }
}
