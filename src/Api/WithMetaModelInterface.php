<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Api;

use Dbout\WpOrm\MetaMappingConfig;

interface WithMetaModelInterface
{
    /**
     * @return MetaMappingConfig
     */
    public function getMetaConfigMapping(): MetaMappingConfig;
}
