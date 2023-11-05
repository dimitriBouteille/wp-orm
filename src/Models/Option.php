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
use Dbout\WpOrm\Providers\YesNo;

/**
 * @method static Option|null find($optionId)
 * @method static OptionBuilder query()
 * @method static OptionBuilder autoload(bool|YesNo $autoload = YesNo::Yes)
 * @method OptionBuilder autoload(bool|YesNo $autoload = YesNo::Yes)
 */
class Option extends AbstractModel implements OptionInterface
{
    /**
     * @inheritDoc
     */
    protected $primaryKey = self::OPTION_ID;

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

    /**
     * @inheritDoc
     */
    public static function findByName(string $optionName): ?self
    {
        return self::query()->firstWhere(self::NAME, $optionName);
    }

    /**
     * @param OptionBuilder $builder
     * @param bool|YesNo $autoload
     * @return void
     */
    public function scopeAutoload(OptionBuilder $builder, bool|YesNo $autoload = YesNo::Yes): void
    {
        if (is_bool($autoload)) {
            $autoload = $autoload ? YesNo::Yes : YesNo::No;
        }

        $builder->where(self::AUTOLOAD, $autoload->value);
    }
}
