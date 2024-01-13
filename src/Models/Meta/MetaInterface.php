<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
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
    public function getFkColumn(): string;


    /**
     * @return string
     */
    public function getKeyColumn(): string;

    /**
     * @return string
     */
    public function getValueColumn(): string;
}
