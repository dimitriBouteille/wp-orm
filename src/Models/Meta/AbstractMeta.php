<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models\Meta;

use Dbout\WpOrm\Orm\AbstractModel;

abstract class AbstractMeta extends AbstractModel
{
    final public const META_KEY = 'meta_key';
    final public const META_VALUE = 'meta_value';

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
     * @return string
     */
    public function getKey(): string
    {
        return $this->getAttribute(self::META_KEY);
    }

    /**
     * @param string $key
     * @return $this
     */
    public function setKey(string $key): self
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
