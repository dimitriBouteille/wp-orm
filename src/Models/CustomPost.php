<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Api\CustomModelTypeInterface;
use Dbout\WpOrm\Exceptions\NotAllowedException;
use Dbout\WpOrm\Scopes\CustomModelTypeScope;

abstract class CustomPost extends Post implements CustomModelTypeInterface
{
    /**
     * @var string
     */
    protected string $_type;

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
    public function getPostType(): ?string
    {
        return $this->_type;
    }

    /**
     * @inheritDoc
     */
    public function getCustomTypeCode(): string
    {
        return $this->getPostType();
    }

    /**
     * @inheritDoc
     */
    public function getCustomTypeColumn(): string
    {
        return self::TYPE;
    }

    /**
     * @param string $type
     * @throws NotAllowedException
     * @return never
     */
    final public function setPostType(string $type): never
    {
        throw new NotAllowedException(sprintf(
            'You cannot set a type for this object. Current type [%s]',
            $this->_type
        ));
    }

    /**
     * @return string|null
     */
    public static function type(): ?string
    {
        return (new static())->getPostType();
    }

    /**
     * @inheritDoc
     */
    protected static function booted()
    {
        static::addGlobalScope(new CustomModelTypeScope());
    }
}
