<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Migrations;

use Dbout\WpOrm\Exceptions\MigrationException;
use Dbout\WpOrm\Orm\Resolver;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Filesystem\Filesystem;

/**
 * Entry point of the migration system, it must be enabled explicitly:
 *
 *     Migrations::enable([
 *         'paths' => [__DIR__ . '/database/migrations'],
 *         'table' => 'migrations',
 *     ]);
 */
class Migrations
{
    protected static ?MigrationConfig $config = null;
    protected static ?Migrator $migrator = null;

    /**
     * Enable the migration system. Only the configuration is checked here, enable()
     * is meant to be called on every request so it must stay cheap.
     *
     * @param array<string, mixed> $config
     * @throws MigrationException
     * @return void
     */
    public static function enable(array $config): void
    {
        if (self::$config instanceof MigrationConfig) {
            throw new MigrationException('The migration system is already enabled.');
        }

        self::$config = MigrationConfig::fromArray($config);
    }

    /**
     * @return bool
     */
    public static function isEnabled(): bool
    {
        return self::$config instanceof MigrationConfig;
    }

    /**
     * @throws MigrationException
     * @return MigrationConfig
     */
    public static function getConfig(): MigrationConfig
    {
        if (!self::$config instanceof MigrationConfig) {
            throw new MigrationException('The migration system is not enabled, call Migrations::enable() first.');
        }

        return self::$config;
    }

    /**
     * @throws MigrationException
     * @return Migrator
     */
    public static function getMigrator(): Migrator
    {
        if (self::$migrator instanceof Migrator) {
            return self::$migrator;
        }

        $config = self::getConfig();
        if (!class_exists(Filesystem::class)) {
            throw new MigrationException(
                'The migration system requires the "illuminate/filesystem" package, run: composer require illuminate/filesystem'
            );
        }

        foreach ($config->paths as $path) {
            if (!is_dir($path)) {
                throw new MigrationException(sprintf('The migration directory "%s" does not exist.', $path));
            }
        }

        $resolver = new Resolver();
        self::$migrator = new Migrator(
            new MigrationRepository($resolver, $config->table),
            $resolver,
            new Filesystem()
        );

        return self::$migrator;
    }

    /**
     * Disable the migration system.
     *
     * @internal Used by the tests.
     * @return void
     */
    public static function reset(): void
    {
        self::$config = null;
        self::$migrator = null;
    }
}
