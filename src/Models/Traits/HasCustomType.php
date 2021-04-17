<?php

namespace Dbout\WpOrm\Models\Traits;

use Dbout\WpOrm\Exceptions\NotAllowedException;

/**
 * Trait HasCustomType
 * @package Dbout\WpOrm\Models\Traits
 */
trait HasCustomType
{

    /**
     * @var string
     */
    protected string $_type;

    /**
     * @return string|null
     */
    public function getPostType(): ?string
    {
        return $this->_type;
    }

    /**
     * @param string $postType
     * @throws NotAllowedException
     */
    public final function setPostType(string $postType): void
    {
        throw new NotAllowedException(sprintf(
            "You cannot set a type for this object. Current type [%s]",
            $this->_type
        ));
    }

    /**
     * @return string|null
     */
    public static function type(): ?string
    {
        return (new static())->getType();
    }
}
