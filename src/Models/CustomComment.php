<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Models\Traits\HasCustomType;
use Dbout\WpOrm\Scopes\CustomPostAddTypeScope;

/**
 * Class CustomComment
 * @package Dbout\WpOrm\Models
 *
 * @author Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @copyright Copyright (c) 2021
 */
abstract class CustomComment extends Comment
{

    use HasCustomType;

    /**
     * CustomPost constructor.
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
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new CustomPostAddTypeScope());
    }
}