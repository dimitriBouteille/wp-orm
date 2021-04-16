<?php

namespace Dbout\WpOrm\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AbstractBuilder
 * @package Dbout\WpOrm\Builders
 */
abstract class AbstractBuilder extends Builder
{

    /**
     * @param Model $model
     * @return $this
     */
    public function setModel(Model $model): self
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
        if(is_array($first)) {
            $this->whereIn($columns, $first);
        } else if(count($value) == 1) {
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
        if($joins == null) {
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
