<?php

namespace Dbout\WpOrm\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class AbstractBuilder
 * @package Dbout\WpOrm\Builders
 */
abstract class AbstractBuilder extends Builder
{

    /**
     * @param string $columns
     * @param $value
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
}
