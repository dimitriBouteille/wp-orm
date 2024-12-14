<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Scopes;

use Dbout\WpOrm\Api\CustomModelTypeInterface;
use Dbout\WpOrm\Exceptions\WpOrmException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CustomModelTypeScope implements Scope
{
    /**
     * @inheritDoc
     * @throws WpOrmException
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (!$model instanceof CustomModelTypeInterface) {
            throw new WpOrmException(sprintf(
                'The object %s must be implement %s.',
                get_class($model),
                CustomModelTypeInterface::class
            ));
        }

        $builder->where($model->getCustomTypeColumn(), $model->getCustomTypeCode());
    }
}
