<?php

namespace Dbout\WpOrm\Migration;

/**
 * Class Config
 * @package Dbout\WpOrm\Migration
 */
class Config
{

    /**
     * @param string|null $migrationsPath
     * @return array
     */
    public static function createPhinxConfig(?string $migrationsPath = null): array
    {
        $config = [
            'migration_base_class' => \Dbout\WpOrm\Migration\AbstractMigration::class,
            'environments' => [
                'default_migration_table' => 'phinxlog',
                'default_database' => 'dev',
                'dev' => [
                    'adapter' => 'mysql',
                    'host' => '',
                    'name' => '',
                    'user' => '',
                    'pass' => '',
                    'table_prefix' => '',
                ]
            ],
        ];

        if ($migrationsPath) {
            $config['paths'] = [
                'migrations' => $migrationsPath,
            ];
        }

        return $config;
    }
}