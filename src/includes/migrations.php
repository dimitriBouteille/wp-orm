<?php

if (!function_exists('wp_orm_get_phinx_config')) {

    /**
     * @param array $config
     * @return array
     */
    function wp_orm_get_phinx_config(array $config = []): array
    {
        return \Dbout\WpOrm\Migration\Config::createPhinxConfig($config);
    }
}