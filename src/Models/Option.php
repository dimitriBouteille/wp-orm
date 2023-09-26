<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Api\OptionInterface;
use Dbout\WpOrm\Builders\OptionBuilder;
use Dbout\WpOrm\Orm\AbstractModel;

/**
 * @method static Option|null find($optionId)
 * @method static OptionBuilder query()
 */
class Option extends AbstractModel implements OptionInterface
{
    /**
     * @inheritDoc
     */
    protected $primaryKey = self::OPTION_ID;

    protected $casts = [
        self::AUTOLOAD => 'boolean',
    ];

    /**
     * @inheritDoc
     */
    protected $table = 'options';

    /**
     * @inheritDoc
     */
    public $timestamps = false;

    /**
     * @inheritDoc
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
