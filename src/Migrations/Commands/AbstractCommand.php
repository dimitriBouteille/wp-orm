<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Migrations\Commands;

use Dbout\WpOrm\Migrations\Events\MigrationNotFound;
use Dbout\WpOrm\Migrations\Migrations;
use Dbout\WpOrm\Migrations\Migrator;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Database\Events\MigrationEnded;
use Illuminate\Database\Events\MigrationSkipped;
use Illuminate\Database\Events\MigrationStarted;
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
     * Whether a command is running, the progress listeners print nothing otherwise.
     */
    private static bool $showProgress = false;
    private static float $startedAt = 0.0;

    /**
     * Event dispatchers on which the progress listeners are registered.
     *
     * @var \WeakMap<DispatcherContract, true>|null
     */
    private static ?\WeakMap $listening = null;

    /**
     * Print each migration while it runs, so the progress is visible even if a later migration fails.
     *
     * @param \Closure(): void $callback
     * @return void
     */
    protected function withProgress(\Closure $callback): void
    {
        $this->listenProgress(Migrations::getEventDispatcher());
        self::$showProgress = true;

        try {
            $callback();
        } finally {
            self::$showProgress = false;
        }
    }

    /**
     * The listeners are registered once and never removed: the dispatcher is public
     * (Migrations::getEventDispatcher()) and the Illuminate dispatcher can only forget
     * all the listeners of an event, including the ones of the users.
     *
     * @param DispatcherContract $events
     * @return void
     */
    private function listenProgress(DispatcherContract $events): void
    {
        self::$listening ??= new \WeakMap();
        if (isset(self::$listening[$events])) {
            return;
        }

        self::$listening[$events] = true;

        $events->listen(MigrationStarted::class, static function (MigrationStarted $event): void {
            if (!self::$showProgress) {
                return;
            }

            self::$startedAt = microtime(true);
            \WP_CLI::log(sprintf(
                '%s: %s',
                $event->method === 'down' ? 'Rolling back' : 'Migrating',
                $event->name ?? get_class($event->migration)
            ));
        });

        $events->listen(MigrationEnded::class, static function (MigrationEnded $event): void {
            if (!self::$showProgress) {
                return;
            }

            \WP_CLI::log(sprintf(
                '%s: %s (%.2fms)',
                $event->method === 'down' ? 'Rolled back' : 'Migrated',
                $event->name ?? get_class($event->migration),
                (microtime(true) - self::$startedAt) * 1000
            ));
        });

        $events->listen(MigrationSkipped::class, static function (MigrationSkipped $event): void {
            if (self::$showProgress) {
                \WP_CLI::log(sprintf('Skipped: %s', $event->migrationName));
            }
        });

        $events->listen(MigrationNotFound::class, static function (MigrationNotFound $event): void {
            if (self::$showProgress) {
                \WP_CLI::warning(sprintf('Migration file not found, %s cannot be rolled back.', $event->migration));
            }
        });
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
