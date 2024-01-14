<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Api;

/**
 * @since 3.0.0
 */
interface CustomModelTypeInterface
{
    /**
     * @return string
     */
    public function getCustomTypeCode(): string;

    /**
     * @return string
     */
    public function getCustomTypeColumn(): string;
}
