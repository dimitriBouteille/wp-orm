<?php

if (!function_exists('wp_orm_get_phinx_config')) {

    /**
     * @param string|null $migrationsPath
     * @return array
     */
    function wp_orm_get_phinx_config(?string $migrationsPath = null): array
    {
        return \Dbout\WpOrm\Migration\Config::createPhinxConfig($migrationsPath);
    }
}