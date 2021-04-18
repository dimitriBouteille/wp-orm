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
 *
 * @method self setOptionName(string $name)
 * @method string getOptionName()
 * @method self setOptionValue($value)
 * @method mixed getOptionValue()
 * @method self setAutoload(string $autoload)
 * @method string getAutoload()
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
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $fillable = [
        self::OPTION_ID, self::NAME, self::VALUE, self::AUTOLOAD,
    ];

    /**
     * @inheritDoc
     */
    public function newEloquentBuilder($query): OptionBuilder
    {
        return new OptionBuilder($query);
    }
}
