<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Orm\Traits\TypeModel;
use Dbout\WpOrm\Scopes\CustomPostAddTypeScope;

/**
 * Class CustomPost
 * @package Dbout\WpOrm\Models
 */
abstract class CustomPost extends Post
{

    use TypeModel;

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