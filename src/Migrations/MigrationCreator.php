<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Migrations;

use Dbout\WpOrm\Exceptions\MigrationException;
use Illuminate\Filesystem\Filesystem;

class MigrationCreator extends \Illuminate\Database\Migrations\MigrationCreator
{
    /**
     * @param Filesystem $files
     * @param string $customStubPath
     * @param array<string> $paths All the configured migration paths
     */
    public function __construct(
        Filesystem $files,
        string $customStubPath,
        protected readonly array $paths,
    ) {
        parent::__construct($files, $customStubPath);
    }

    /**
     * Illuminate loads every file of the directory and checks that no class is named after the migration,
     * this does not work with anonymous classes and fails for names like "walker" (WordPress Walker class).
     * The name is checked against the file names of all the configured paths instead: two migrations
     * with the same name would be hard to tell apart in "wp orm migrate:status".
     *
     * @inheritDoc
     * @throws MigrationException
     */
    protected function ensureMigrationDoesntAlreadyExist($name, $migrationPath = null)
    {
        foreach ($this->paths as $path) {
            foreach ($this->files->glob($path . '/*.php') ?: [] as $file) {
                if (preg_match('/^\d{4}_\d{2}_\d{2}_\d{6}_' . preg_quote($name, '/') . '\.php\z/', basename($file)) === 1) {
                    throw new MigrationException(sprintf('A migration named "%s" already exists: %s', $name, $file));
                }
            }
        }
    }
}
