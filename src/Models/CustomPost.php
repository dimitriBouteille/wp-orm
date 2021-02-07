<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Exceptions\NotAllowedException;
use Dbout\WpOrm\Scopes\CustomPostAddTypeScope;

/**
 * Class CustomPost
 * @package Dbout\WpOrm\Models
 */
abstract class CustomPost extends Post
{

    /**
     * @var string
     */
    protected string $_type;

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
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->_type;
    }

    /**
     * @param string $postType
     * @return Post
     * @throws NotAllowedException
     */
    public function setType(string $postType): Post
    {
        throw new NotAllowedException(sprintf("You cannot set a type for this object. Current type [%s]", $this->_type));
        return $this;
    }

    /**
     * @return string|null
     */
    public static function type(): ?string
    {
        return (new static())->getType();
    }

    /**
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new CustomPostAddTypeScope());
    }
}