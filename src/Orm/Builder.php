<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Orm;

use Illuminate\Database\Query\Builder as EloquentBuilder;

class Builder extends EloquentBuilder
{
    /**
     * Add an exists clause to the query.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    public function addWhereExistsQuery(EloquentBuilder $query, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotExists' : 'Exists';
        $this->wheres[] = ['type' => $type, 'query' => $query, 'boolean' => $boolean];

        $this->addBinding($query->getBindings(), 'where');

        return $this;
    }

    /**
     * @return Database|\Illuminate\Database\ConnectionInterface
     */
    public function getConnection()
    {
        return Database::getInstance();
    }
}
