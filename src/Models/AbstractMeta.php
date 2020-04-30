<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Contracts\MetaInterface;
use Dbout\WpOrm\Orm\AbstractModel;

/**
 * Class AbstractMeta
 * @package Dbout\WpOrm\Models
 */
abstract class AbstractMeta extends AbstractModel implements MetaInterface
{

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->getAttribute(self::META_KEY);
    }

    /**
     * @param string $key
     * @return MetaInterface
     */
    public function setKey(string $key): MetaInterface
    {
        $this->setAttribute(self::META_KEY, $key);
        return $this;
    }

    /**
     * @return mixed|void
     */
    public function getValue()
    {
        return $this->getAttribute(self::META_VALUE);
    }

    /**
     * @param string $value
     * @return MetaInterface
     */
    public function setValue(string $value): MetaInterface
    {
        $this->setAttribute(self::META_VALUE, $value);
        return $this;
    }

}