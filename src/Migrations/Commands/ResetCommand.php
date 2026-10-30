<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Migrations\Commands;

class ResetCommand extends AbstractCommand
{
    /**
     * Roll back all the migrations.
     *
     * ## OPTIONS
     *
     * [--yes]
     * : Answer yes to the confirmation message.
     *
     * ## EXAMPLES
     *
     *     wp orm migrate:reset
     *     wp orm migrate:reset --yes
     *
     * @param array<string> $args
     * @param array<string, mixed> $assocArgs
     * @return void
     */
    public function __invoke(array $args, array $assocArgs): void
    {
        $this->execute(function () use ($assocArgs): void {
            $migrator = $this->migrator();
            if (!$migrator->repositoryExists()) {
                \WP_CLI::warning('Migration table not found, nothing to reset.');
                return;
            }

            $this->confirm('Are you sure you want to roll back all the migrations?', $assocArgs);

            $rolledBack = [];
            $this->withProgress(function () use ($migrator, &$rolledBack): void {
                $rolledBack = $migrator->reset($this->paths());
            });

            if ($rolledBack === []) {
                \WP_CLI::success('Nothing to roll back.');
                return;
            }

            \WP_CLI::success(sprintf('%d migration(s) rolled back.', count($rolledBack)));
        });
    }
}
