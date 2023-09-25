<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Scopes;

use Dbout\WpOrm\Models\CustomPost;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CustomPostAddTypeScope implements Scope
{
    /**
     * @inheritDoc
     */
    public function apply(Builder $builder, Model $model)
    {
        /** @var CustomPost $model */
        $type = $model->getPostType();
        $builder->whereTypes($type);
    }
}
