<?php

namespace Dbout\WpOrm\Scopes;

use Dbout\WpOrm\Models\CustomPost;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Class CustomPostAddTypeScope
 * @package Dbout\WpOrm\Scopes
 */
class CustomPostAddTypeScope implements Scope
{

    /**
     * @param Builder $builder
     * @param Model $model
     */
    public function apply(Builder $builder, Model $model)
    {
        /** @var CustomPost $model */
        $type = $model->getType();
        $builder->whereTypes($type);
    }
}
