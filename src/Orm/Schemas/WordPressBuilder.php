<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Orm\Schemas;

use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Schema\Grammars\MySqlGrammar;
use Illuminate\Database\Schema\MySqlBuilder;

class WordPressBuilder extends MySqlBuilder
{
    /**
     * @var MySqlGrammar
     */
    protected $grammar;

    /**
     * @inheritDoc
     */
    public function getColumns($table): array
    {
        /**
         * Never add prefix table because the model::getTable contain the prefix
         * @see AbstractModel::getTable()
         */
        $results = $this->connection->selectFromWriteConnection(
            $this->grammar->compileColumns($this->connection->getDatabaseName(), $table)
        );

        return $this->connection->getPostProcessor()->processColumns($results);
    }
}
