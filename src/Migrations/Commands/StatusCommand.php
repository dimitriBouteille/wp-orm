<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Migrations\Commands;

use WP_CLI\Utils;

class StatusCommand extends AbstractCommand
{
    public const string STATUS_RAN = 'Ran';
    public const string STATUS_PENDING = 'Pending';

    /**
     * Show the status of each migration.
     *
     * ## OPTIONS
     *
     * [--format=<format>]
     * : Render output in a particular format.
     * ---
     * default: table
     * options:
     *   - table
     *   - csv
     *   - json
     *   - yaml
     * ---
     *
     * ## EXAMPLES
     *
     *     wp orm migrate:status
     *     wp orm migrate:status --format=json
     *
     * @param array<string> $args
     * @param array<string, mixed> $assocArgs
     * @return void
     */
    public function __invoke(array $args, array $assocArgs): void
    {
        $this->execute(function () use ($assocArgs): void {
            $migrator = $this->migrator();
            $files = $migrator->getMigrationFiles($this->paths());
            if ($files === []) {
                \WP_CLI::warning('No migrations found.');
                return;
            }

            $batches = [];
            if ($migrator->repositoryExists()) {
                $batches = $migrator->getRepository()->getMigrationBatches();
            } else {
                \WP_CLI::warning('Migration table not found, run "wp orm migrate" to create it.');
            }

            $format = $assocArgs['format'] ?? 'table';
            Utils\format_items(
                is_string($format) ? $format : 'table',
                $this->getRows(array_keys($files), $batches),
                ['migration', 'batch', 'status']
            );
        });
    }

    /**
     * @param array<string> $migrations Migration names, sorted
     * @param array<string, int> $batches Batch number of each migration that ran
     * @return array<array{migration: string, batch: int|null, status: string}>
     */
    public function getRows(array $migrations, array $batches): array
    {
        return array_map(static fn (string $migration): array => [
            'migration' => $migration,
            'batch' => $batches[$migration] ?? null,
            'status' => isset($batches[$migration]) ? self::STATUS_RAN : self::STATUS_PENDING,
        ], $migrations);
    }
}
