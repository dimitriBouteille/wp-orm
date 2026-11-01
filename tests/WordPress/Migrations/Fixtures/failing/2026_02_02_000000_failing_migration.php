<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */
use Dbout\WpOrm\Migrations\Migration;
use Dbout\WpOrm\Tests\WordPress\Migrations\FailingMigrationException;
use Illuminate\Database\Schema\Blueprint;

return new class () extends Migration {
    public function up(): void
    {
        $this->schema()->create('mig_partial', function (Blueprint $table) {
            $table->id();
        });

        throw new FailingMigrationException('Migration failed.');
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('mig_partial');
    }
};
