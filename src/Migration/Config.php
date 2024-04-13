<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Migration;

/**
 * @deprecated Remove in future version
 * @see https://github.com/dimitriBouteille/wp-orm/issues/27
 */
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
                'seeds' => $userConfig['seeds_path'] ?? null,
            ],
            'environments' => [
                'default_migration_table' => $userConfig['migration_table'] ?? 'phinxlog',
                'default_database' => $userConfig['default_database'] ?? 'default',
                'default' => [
                    'adapter' => $userConfig['adapter'] ?? 'mysql',
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
