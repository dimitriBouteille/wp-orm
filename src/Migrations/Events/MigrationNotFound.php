<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Migrations\Events;

use Illuminate\Contracts\Database\Events\MigrationEvent;

/**
 * Dispatched when a migration that ran must be rolled back but its file does not exist anymore
 * (file deleted or renamed, plugin removed...). The migration is not rolled back.
 */
class MigrationNotFound implements MigrationEvent
{
    /**
     * @param string $migration Migration name
     */
    public function __construct(
        public readonly string $migration,
    ) {
    }
}
