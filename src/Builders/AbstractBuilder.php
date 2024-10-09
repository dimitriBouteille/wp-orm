<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractBuilder extends Builder
{
    /**
     * @inheritDoc
     */
    public function setModel(Model $model)
    {
        parent::setModel($model);
        $this->select(sprintf('%s.*', $this->model->getTable()));

        return $this;
    }

    /**
     * @param string $columns
     * @param array $value
     * @return $this
     */
    protected function _whereOrIn(string $columns, array $value): self
    {
        $first = reset($value);
        if (is_array($first)) {
            $this->whereIn($columns, $first);
        } elseif (count($value) == 1) {
            $this->where($columns, reset($value));
        } else {
            $this->whereIn($columns, $value);
        }

        return $this;
    }

    /**
     * @param $query
     * @param $table
     * @return bool
     */
    protected function joined($query, $table): bool
    {
        $joins = $query->getQuery()->joins;
        if ($joins == null) {
            return false;
        }

        foreach ($joins as $join) {
            if ($join->table == $table) {
                return true;
            }
        }

        return false;
    }
}
