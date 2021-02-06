<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Builders\OptionBuilder;
use Dbout\WpOrm\Orm\AbstractModel;

/**
 * Class Option
 * @package Dbout\WpOrm\Models
 *
 * @method static Option|null find($optionId)
 * @method static OptionBuilder query()
 */
class Option extends AbstractModel
{

    const OPTION_ID = 'option_id';
    const NAME = 'option_name';
    const VALUE = 'option_value';
    const AUTOLOAD = 'autoload';

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
        return $this->getAttribute(self::NAME);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->setAttribute(self::NAME, $name);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->getAttribute(self::VALUE);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value): self
    {
        $this->setAttribute(self::VALUE, $value);
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
     * @return $this
     */
    public function setAutoload(string $autoload): self
    {
        $this->setAttribute(self::AUTOLOAD, $autoload);
        return $this;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return OptionBuilder
     */
    public function newEloquentBuilder($query): OptionBuilder
    {
        return new OptionBuilder($query);
    }
}
