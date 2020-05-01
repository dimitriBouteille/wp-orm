<?php

namespace Dbout\WpOrm\Builders;

use Dbout\WpOrm\Contracts\PostInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class PostBuilder
 * @package Dbout\WpOrm\Builders
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
class PostBuilder extends Builder
{

    /**
     * @param mixed ...$types
     * @return $this
     */
    public function types(...$types)
    {
        return $this->_whereOrIn(PostInterface::POST_TYPE, $types);
    }

    /**
     * @param $author
     * @return $this
     */
    public function author($author)
    {
        $this->where(PostInterface::POST_AUTHOR, $author);
        return $this;
    }

    /**
     * @param mixed ...$status
     * @return $this
     */
    public function status(...$status)
    {
        return $this->_whereOrIn(PostInterface::POST_STATUS, $status);
    }

    /**
     * @param string $columns
     * @param $value
     * @return $this
     */
    protected function _whereOrIn(string $columns, array $value)
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