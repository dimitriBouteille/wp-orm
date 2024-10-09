<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Api\CustomModelTypeInterface;
use Dbout\WpOrm\Exceptions\CannotOverrideCustomTypeException;
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
        parent::__construct(array_merge($attributes, [
            self::TYPE => $this->_type,
        ]));
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
        throw new CannotOverrideCustomTypeException($this->_type);
    }

    /**
     * @inheritDoc
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new CustomModelTypeScope());
    }
}
