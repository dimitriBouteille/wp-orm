<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

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
