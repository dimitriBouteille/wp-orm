<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Api;

/**
 * @method self setOptionName(string $name)
 * @method string getOptionName()
 * @method self setOptionValue($value)
 * @method mixed getOptionValue()
 * @method self setAutoload(string $autoload)
 * @method string getAutoload()
 */
interface OptionInterface
{
    public const OPTION_ID = 'option_id';
    public const NAME = 'option_name';
    public const VALUE = 'option_value';
    public const AUTOLOAD = 'autoload';
}
