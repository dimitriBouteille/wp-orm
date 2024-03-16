<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Orm\Schemas;

use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Schema\MySqlBuilder;

class WordPressBuilder extends MySqlBuilder
{
    /**
     * @inheritDoc
     */
    public function getColumns($table): array
    {
        /**
         * Never add prefix table because the model::getTable contain the prefix
         * @see AbstractModel::getTable()
         */
        return $this->connection->getPostProcessor()->processColumns(
            $this->connection->selectFromWriteConnection($this->grammar->compileColumns($table))
        );
    }
}
