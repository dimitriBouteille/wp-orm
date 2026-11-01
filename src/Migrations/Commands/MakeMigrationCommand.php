<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Migrations\Commands;

use Dbout\WpOrm\Exceptions\MigrationException;
use Dbout\WpOrm\Migrations\Migrations;
use Illuminate\Database\Console\Migrations\TableGuesser;
use Illuminate\Support\Str;

class MakeMigrationCommand extends AbstractCommand
{
    /**
     * Create a new migration file.
     *
     * The table is guessed from the name when the --create and --table options are not used:
     * "create_orders_table" creates the "orders" table, "add_status_to_orders_table" updates it.
     *
     * ## OPTIONS
     *
     * <name>
     * : The name of the migration, e.g. create_orders_table.
     *
     * [--create=<table>]
     * : The table to create, without the WordPress prefix.
     *
     * [--table=<table>]
     * : The table to update, without the WordPress prefix.
     *
     * [--path=<path>]
     * : The directory of the migration, it must be one of the configured paths. Defaults to the first one.
     *
     * ## EXAMPLES
     *
     *     wp orm make:migration create_orders_table
     *     wp orm make:migration add_status_to_orders_table
     *     wp orm make:migration create_orders_table --create=shop_orders --path=wp-content/plugins/shop/migrations
     *
     * @param array<string> $args
     * @param array<string, mixed> $assocArgs
     * @return void
     */
    public function __invoke(array $args, array $assocArgs): void
    {
        $this->execute(function () use ($args, $assocArgs): void {
            $name = Str::snake(trim($args[0] ?? ''));
            if (preg_match('/^\w+\z/', $name) !== 1) {
                throw new MigrationException(sprintf(
                    'Invalid migration name "%s", only letters, numbers and underscores are allowed.',
                    $args[0] ?? ''
                ));
            }

            [$table, $create] = $this->getTable($name, $assocArgs);
            $path = $this->getPath($assocArgs);
            $this->ensureMigrationDoesntExist($name);

            $file = Migrations::getMigrationCreator()->create($name, $path, $table, $create);
            \WP_CLI::success(sprintf('Migration created: %s', $file));
        });
    }

    /**
     * @param string $name
     * @param array<string, mixed> $assocArgs
     * @throws MigrationException
     * @return array{string|null, bool}
     */
    protected function getTable(string $name, array $assocArgs): array
    {
        $create = $this->getTableOption($assocArgs, 'create');
        $table = $this->getTableOption($assocArgs, 'table');

        if ($create !== null && $table !== null) {
            throw new MigrationException('The --create and --table options cannot be used together.');
        }

        if ($create !== null) {
            return [$create, true];
        }

        if ($table !== null) {
            return [$table, false];
        }

        return TableGuesser::guess($name) ?? [null, false];
    }

    /**
     * @param array<string, mixed> $assocArgs
     * @param string $name
     * @throws MigrationException
     * @return string|null
     */
    protected function getTableOption(array $assocArgs, string $name): ?string
    {
        if (!isset($assocArgs[$name])) {
            return null;
        }

        // The table name is written in the generated PHP code.
        $table = $assocArgs[$name];
        if (!is_string($table) || preg_match('/^\w{1,64}\z/', $table) !== 1) {
            throw new MigrationException(sprintf(
                'The --%s option must be a table name, only letters, numbers and underscores are allowed.',
                $name
            ));
        }

        return $table;
    }

    /**
     * The migration must be created in a configured path, otherwise it would never run.
     *
     * @param array<string, mixed> $assocArgs
     * @throws MigrationException
     * @return string
     */
    protected function getPath(array $assocArgs): string
    {
        $paths = $this->paths();
        if (!isset($assocArgs['path'])) {
            return $paths[0];
        }

        $path = is_string($assocArgs['path']) ? $assocArgs['path'] : '';
        $realPath = realpath($path);
        foreach ($paths as $configuredPath) {
            if (rtrim($configuredPath, '/') === rtrim($path, '/')
                || ($realPath !== false && $realPath === realpath($configuredPath))) {
                return $configuredPath;
            }
        }

        throw new MigrationException(sprintf(
            'The path "%s" is not a configured migration path, available paths: %s',
            $path,
            implode(', ', $paths)
        ));
    }

    /**
     * Two migrations with the same name would be hard to tell apart in "wp orm migrate:status".
     *
     * @param string $name
     * @throws MigrationException
     * @return void
     */
    protected function ensureMigrationDoesntExist(string $name): void
    {
        foreach ($this->paths() as $path) {
            foreach (glob($path . '/*.php') ?: [] as $file) {
                if (preg_match('/^\d{4}_\d{2}_\d{2}_\d{6}_' . $name . '\.php\z/', basename($file)) === 1) {
                    throw new MigrationException(sprintf('A migration named "%s" already exists: %s', $name, $file));
                }
            }
        }
    }
}
