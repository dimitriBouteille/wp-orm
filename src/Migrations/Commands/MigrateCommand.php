<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Migrations\Commands;

class MigrateCommand extends AbstractCommand
{
    /**
     * Run the pending migrations.
     *
     * ## OPTIONS
     *
     * [--step]
     * : Run each migration in its own batch, so they can be rolled back individually.
     *
     * ## EXAMPLES
     *
     *     wp orm migrate
     *     wp orm migrate --step
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
                $migrator->getRepository()->createRepository();
                \WP_CLI::log('Migration table created.');
            }

            $ran = [];
            $this->withProgress(function () use ($migrator, $assocArgs, &$ran): void {
                $ran = $migrator->run($this->paths(), [
                    'step' => isset($assocArgs['step']) && $assocArgs['step'] !== false,
                ]);
            });

            if ($ran === []) {
                \WP_CLI::success('Nothing to migrate.');
                return;
            }

            \WP_CLI::success(sprintf('%d migration(s) ran.', count($ran)));
        });
    }
}
