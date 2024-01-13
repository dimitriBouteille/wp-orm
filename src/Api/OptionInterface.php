<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Api;

use Dbout\WpOrm\Enums\YesNo;
use Dbout\WpOrm\Models\Option;

/**
 * @method Option setOptionName(string $name)
 * @method string getOptionName()
 * @method Option setOptionValue($value)
 * @method mixed getOptionValue()
 * @method Option setAutoload(string|YesNo $autoload)
 * @method string getAutoload()
 */
interface OptionInterface
{
    public const OPTION_ID = 'option_id';
    public const NAME = 'option_name';
    public const VALUE = 'option_value';
    public const AUTOLOAD = 'autoload';

    /**
     * Find one option by name
     *
     * @param string $optionName
     * @return self|null
     */
    public static function findOneByName(string $optionName): ?self;
}
