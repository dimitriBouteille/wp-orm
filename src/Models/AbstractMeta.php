<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Contracts\MetaInterface;
use Dbout\WpOrm\Orm\AbstractModel;

/**
 * Class AbstractMeta
 * @package Dbout\WpOrm\Models
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
abstract class AbstractMeta extends AbstractModel implements MetaInterface
{

    /**
     * Disable created_at and updated_at
     * @var bool
     */
    public $timestamps = false;

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