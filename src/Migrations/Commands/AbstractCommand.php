<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Migrations\Commands;

use Dbout\WpOrm\Migrations\Migrations;
use Illuminate\Database\Events\MigrationEnded;
use Illuminate\Database\Events\MigrationSkipped;
use Illuminate\Database\Events\MigrationStarted;
use Illuminate\Database\Migrations\Migrator;
use WP_CLI\ExitException;

abstract class AbstractCommand
{
    /**
     * Run the command and turn any exception into a WP-CLI error (exit code 1).
     *
     * @param \Closure(): void $callback
     * @throws ExitException
     * @return void
     */
    protected function execute(\Closure $callback): void
    {
        try {
            $callback();
        } catch (ExitException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            \WP_CLI::error($exception->getMessage());
        }
    }

    /**
     * Print each migration while it runs, so the progress is visible even if a later migration fails.
     *
     * @param \Closure(): void $callback
     * @return void
     */
    protected function withProgress(\Closure $callback): void
    {
        $events = Migrations::getEventDispatcher();
        $startedAt = 0.0;

        $events->listen(MigrationStarted::class, function (MigrationStarted $event) use (&$startedAt): void {
            $startedAt = microtime(true);
            \WP_CLI::log(sprintf(
                '%s: %s',
                $event->method === 'down' ? 'Rolling back' : 'Migrating',
                $event->name ?? get_class($event->migration)
            ));
        });

        $events->listen(MigrationEnded::class, function (MigrationEnded $event) use (&$startedAt): void {
            \WP_CLI::log(sprintf(
                '%s: %s (%.2fms)',
                $event->method === 'down' ? 'Rolled back' : 'Migrated',
                $event->name ?? get_class($event->migration),
                (microtime(true) - $startedAt) * 1000
            ));
        });

        $events->listen(MigrationSkipped::class, function (MigrationSkipped $event): void {
            \WP_CLI::log(sprintf('Skipped: %s', $event->migrationName));
        });

        try {
            $callback();
        } finally {
            $events->forget(MigrationStarted::class);
            $events->forget(MigrationEnded::class);
            $events->forget(MigrationSkipped::class);
        }
    }

    /**
     * @param string $question
     * @param array<string, mixed> $assocArgs
     * @return void
     */
    protected function confirm(string $question, array $assocArgs): void
    {
        \WP_CLI::confirm($question, $assocArgs);
    }

    /**
     * @return Migrator
     */
    protected function migrator(): Migrator
    {
        return Migrations::getMigrator();
    }

    /**
     * @return array<string>
     */
    protected function paths(): array
    {
        return Migrations::getConfig()->paths;
    }

    /**
     * @param array<string, mixed> $assocArgs
     * @param string $name
     * @return int|null
     */
    protected function getPositiveInt(array $assocArgs, string $name): ?int
    {
        if (!isset($assocArgs[$name])) {
            return null;
        }

        $value = filter_var($assocArgs[$name], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($value === false) {
            \WP_CLI::error(sprintf('The --%s option must be a positive integer.', $name));
        }

        return $value;
    }
}
