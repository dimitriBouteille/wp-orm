<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Orm\Query\Processors;

use Dbout\WpOrm\Orm\Database;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Processors\Processor;

class WordPressProcessor extends Processor
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
