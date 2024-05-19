<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Orm\Query\Grammars;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Grammars\MySqlGrammar;

/**
 * @todo Extend from MySqlGrammar next major version
 * @see MySqlGrammar
 */
class WordPressGrammar extends Grammar
{
    /**
     * @inheritDoc
     */
    public function compileUpsert(Builder $query, array $values, array $uniqueBy, array $update): string
    {
        // @phpstan-ignore-next-line
        $useUpsertAlias = $query->connection->getConfig('use_upsert_alias');

        $sql = $this->compileInsert($query, $values);

        if ($useUpsertAlias) {
            $sql .= ' as laravel_upsert_alias';
        }

        $sql .= ' on duplicate key update ';

        $columns = collect($update)->map(function ($value, $key) use ($useUpsertAlias) {
            if (! is_numeric($key)) {
                return $this->wrap($key).' = '.$this->parameter($value);
            }

            return $useUpsertAlias
                ? $this->wrap($value).' = '.$this->wrap('laravel_upsert_alias').'.'.$this->wrap($value)
                : $this->wrap($value).' = values('.$this->wrap($value).')';
        })->implode(', ');

        return $sql.$columns;
    }
}
