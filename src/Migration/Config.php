<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Migration;

class Config
{
    /**
     * @param array $userConfig
     * @return array
     */
    public static function createPhinxConfig(array $userConfig = []): array
    {
        return [
            'migration_base_class' => \Phinx\Migration\AbstractMigration::class,
            'paths' => [
                'migrations' => $userConfig['migrations_path'],
            ],
            'environments' => [
                'default_migration_table' => 'phinxlog',
                'default_database' => 'default',
                'default' => [
                    'adapter' => 'mysql',
                    'host' => $userConfig['db_host'] ?? '',
                    'name' => $userConfig['db_name'] ?? '',
                    'user' => $userConfig['db_user'] ?? '',
                    'pass' => $userConfig['db_password'] ?? '',
                    'table_prefix' => $userConfig['table_prefix'] ?? '',
                ],
            ],
        ];
    }
}
