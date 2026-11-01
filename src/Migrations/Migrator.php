<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Migrations;

use Dbout\WpOrm\Migrations\Events\MigrationNotFound;

class Migrator extends \Illuminate\Database\Migrations\Migrator
{
    /**
     * Illuminate skips a migration whose file does not exist anymore and only reports it to the console
     * output, which is not used here: a MigrationNotFound event is dispatched instead.
     *
     * @inheritDoc
     */
    protected function rollbackMigrations(array $migrations, $paths, array $options)
    {
        $files = $this->getMigrationFiles($paths);
        foreach ($migrations as $migration) {
            $name = ((object) $migration)->migration;
            if (!isset($files[$name])) {
                $this->fireMigrationEvent(new MigrationNotFound($name));
            }
        }

        return parent::rollbackMigrations($migrations, $paths, $options);
    }
}
