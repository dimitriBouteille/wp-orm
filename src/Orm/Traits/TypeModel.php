<?php

namespace Dbout\WpOrm\Orm\Traits;

use Dbout\WpOrm\Exceptions\NotAllowedException;

/**
 * Trait TypeModel
 * @package Dbout\WpOrm\Orm\Traits
 */
trait TypeModel
{

    /**
     * @var string
     */
    protected string $_type;

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->_type;
    }

    /**
     * @param string $postType
     * @throws NotAllowedException
     */
    public final function setType(string $postType): void
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
