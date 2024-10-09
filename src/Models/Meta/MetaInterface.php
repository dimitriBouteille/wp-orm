<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models\Meta;

interface MetaInterface
{
    /**
     * @return string
     */
    public static function getMetaFkColumn(): string;

    /**
     * @return string
     */
    public static function getMetaKeyColumn(): string;

    /**
     * @return string
     */
    public static function getMetaValueColumn(): string;
}
