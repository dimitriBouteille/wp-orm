<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Api;

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
