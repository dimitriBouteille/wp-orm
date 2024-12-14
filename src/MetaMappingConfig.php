<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm;

class MetaMappingConfig
{
    /**
     * @param string $metaClass Meta className
     * @param string $foreignKey
     * @param string $columnKey Column contains the meta key
     * @param string $columnValue Column contains the meta value
     */
    public function __construct(
        public readonly string $metaClass,
        public readonly string $foreignKey,
        public readonly string $columnKey = 'meta_key',
        public readonly string $columnValue = 'meta_value',
    ) {
    }
}
