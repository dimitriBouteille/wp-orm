<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Models\Traits\HasCustomType;
use Dbout\WpOrm\Scopes\CustomPostAddTypeScope;

abstract class CustomPost extends Post
{
    use HasCustomType;

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = [
            self::TYPE => $this->_type,
        ];

        parent::__construct($attributes);
    }

    /**
     * @inheritDoc
     */
    protected static function booted()
    {
        static::addGlobalScope(new CustomPostAddTypeScope());
    }
}
