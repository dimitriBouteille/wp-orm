<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Migrations\Commands;

class RollbackCommand extends AbstractCommand
{
    /**
     * Roll back the last batch of migrations.
     *
     * ## OPTIONS
     *
     * [--step=<step>]
     * : Number of migrations to roll back, whatever their batch.
     *
     * [--batch=<batch>]
     * : Roll back the migrations of this batch.
     *
     * [--yes]
     * : Answer yes to the confirmation message.
     *
     * ## EXAMPLES
     *
     *     wp orm migrate:rollback
     *     wp orm migrate:rollback --step=2
     *     wp orm migrate:rollback --batch=3 --yes
     *
     * @param array<string> $args
     * @param array<string, mixed> $assocArgs
     * @return void
     */
    public function __invoke(array $args, array $assocArgs): void
    {
        $step = $this->getPositiveInt($assocArgs, 'step');
        $batch = $this->getPositiveInt($assocArgs, 'batch');
        if ($step !== null && $batch !== null) {
            \WP_CLI::error('The --step and --batch options cannot be used together.');
        }

        $this->execute(function () use ($assocArgs, $step, $batch): void {
            $migrator = $this->migrator();
            if (!$migrator->repositoryExists()) {
                \WP_CLI::warning('Migration table not found, nothing to roll back.');
                return;
            }

            $this->confirm(match (true) {
                $step !== null => sprintf('Are you sure you want to roll back the last %d migration(s)?', $step),
                $batch !== null => sprintf('Are you sure you want to roll back the batch %d?', $batch),
                default => 'Are you sure you want to roll back the last batch of migrations?',
            }, $assocArgs);

            $rolledBack = [];
            $this->withProgress(function () use ($migrator, $step, $batch, &$rolledBack): void {
                $rolledBack = $migrator->rollback($this->paths(), [
                    'step' => $step ?? 0,
                    'batch' => $batch ?? 0,
                ]);
            });

            if ($rolledBack === []) {
                \WP_CLI::success('Nothing to roll back.');
                return;
            }

            \WP_CLI::success(sprintf('%d migration(s) rolled back.', count($rolledBack)));
        });
    }
}
