<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

if (!function_exists('wp_orm_get_phinx_config')) {

    /**
     * @param array $config
     * @return array
     * @deprecated Remove in future version
     * @see https://github.com/dimitriBouteille/wp-orm/issues/27
     */
    function wp_orm_get_phinx_config(array $config = []): array
    {
        return \Dbout\WpOrm\Migration\Config::createPhinxConfig($config);
    }
}
