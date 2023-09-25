<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Builders\OptionBuilder;
use Dbout\WpOrm\Orm\AbstractModel;

/**
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
    public const OPTION_ID = 'option_id';
    public const NAME = 'option_name';
    public const VALUE = 'option_value';
    public const AUTOLOAD = 'autoload';

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
