<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Contracts\OptionInterface;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Option
 * @package Dbout\WpOrm\Models
 *
 * @method static OptionInterface   find(int $optionId);
 * @method static Builder           name(string $optionName);
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
class Option extends AbstractModel implements OptionInterface
{

    /**
     * @var string
     */
    protected $primaryKey = self::OPTION_ID;

    /**
     * @var string
     */
    protected $table = 'options';

    /**
     * Disable created_at and updated_at
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getAttribute(self::OPTION_NAME);
    }

    /**
     * @param string $name
     * @return OptionInterface
     */
    public function setName(string $name): OptionInterface
    {
        $this->setAttribute(self::OPTION_NAME, $name);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->getAttribute(self::OPTION_VALUE);
    }

    /**
     * @param $value
     * @return OptionInterface
     */
    public function setValue($value): OptionInterface
    {
        $this->setAttribute(self::OPTION_VALUE, $value);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAutoload(): ?string
    {
        return $this->getAttribute(self::AUTOLOAD);
    }

    /**
     * @param string $autoload
     * @return OptionInterface
     */
    public function setAutoload(string $autoload): OptionInterface
    {
        $this->setAttribute(self::AUTOLOAD, $autoload);
        return $this;
    }

    /**
     * @param Builder $builder
     * @param string $name
     */
    public function scopeName(Builder $builder, string $name)
    {
        $builder->where(self::OPTION_NAME, $name);
    }
}
