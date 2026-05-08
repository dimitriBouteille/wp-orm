<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Models\Meta;

use Dbout\WpOrm\Orm\AbstractModel;

abstract class AbstractMeta extends AbstractModel
{
    final public const string META_KEY = 'meta_key';
    final public const string META_VALUE = 'meta_value';

    /**
     * Disable created_at and updated_at
     * @var bool
     */
    public $timestamps = false;

    /**
     * @inheritDoc
     */
    protected $fillable = [
        self::META_VALUE,
        self::META_KEY,
    ];

    /**
     * @deprecated Use {@see self::getMetaKey()} instead. This method shadows
     *             {@see \Illuminate\Database\Eloquent\Model::getKey()} which is expected
     *             to return the primary key value. Will be removed in the next major version.
     * @return string
     */
    public function getKey(): string
    {
        return $this->getMetaKey();
    }

    /**
     * @deprecated Use {@see self::setMetaKey()} instead. Will be removed in the next major version.
     * @param string $key
     * @return $this
     */
    public function setKey(string $key): self
    {
        return $this->setMetaKey($key);
    }

    /**
     * Get the meta key.
     *
     * @return string
     */
    public function getMetaKey(): string
    {
        return $this->getAttribute(self::META_KEY);
    }

    /**
     * Set the meta key.
     *
     * @param string $key
     * @return $this
     */
    public function setMetaKey(string $key): self
    {
        $this->setAttribute(self::META_KEY, $key);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->getAttribute(self::META_VALUE);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue(string $value): self
    {
        $this->setAttribute(self::META_VALUE, $value);
        return $this;
    }
}
