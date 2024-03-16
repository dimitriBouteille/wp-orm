<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Orm;

use Illuminate\Database\Query\Builder;

class Processor extends \Illuminate\Database\Query\Processors\Processor
{
    /**
     * @inheritDoc
     */
    public function processInsertGetId(Builder $query, $sql, $values, $sequence = null): ?int
    {
        /** @var Database $co */
        $co = $query->getConnection();
        $co->insert($sql, $values);

        $id = $co->lastInsertId();
        return is_numeric($id) ? (int) $id : $id;
    }
}
