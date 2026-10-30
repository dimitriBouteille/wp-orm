<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Migrations;

use Dbout\WpOrm\Exceptions\MigrationException;
use Dbout\WpOrm\Migrations\Commands\MigrateCommand;
use Dbout\WpOrm\Migrations\Commands\ResetCommand;
use Dbout\WpOrm\Migrations\Commands\RollbackCommand;
use Dbout\WpOrm\Migrations\Commands\StatusCommand;
use Dbout\WpOrm\Orm\Resolver;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;

/**
 * Entry point of the migration system, it must be enabled explicitly:
 *
 *     Migrations::enable([
 *         'paths' => [__DIR__ . '/database/migrations'],
 *         'table' => 'orm_migrations',
 *     ]);
 */
class Migrations
{
    /**
     * WP-CLI commands registered when the migration system is enabled.
     */
    public const array COMMANDS = [
        'orm migrate' => MigrateCommand::class,
        'orm migrate:rollback' => RollbackCommand::class,
        'orm migrate:status' => StatusCommand::class,
        'orm migrate:reset' => ResetCommand::class,
    ];

    protected static ?MigrationConfig $config = null;
    protected static ?Migrator $migrator = null;
    protected static ?DispatcherContract $events = null;

    /**
     * Enable the migration system and register the WP-CLI commands. Only the configuration
     * is checked here, enable() is meant to be called on every request so it must stay cheap.
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

        if (defined('WP_CLI') && WP_CLI && class_exists(\WP_CLI::class)) {
            foreach (self::COMMANDS as $name => $command) {
                \WP_CLI::add_command($name, $command);
            }
        }
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
            new Filesystem(),
            self::getEventDispatcher()
        );

        return self::$migrator;
    }

    /**
     * Events dispatched by the migrator (MigrationStarted, MigrationEnded...).
     *
     * @return DispatcherContract
     */
    public static function getEventDispatcher(): DispatcherContract
    {
        return self::$events ??= new Dispatcher();
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
        self::$events = null;
    }
}
