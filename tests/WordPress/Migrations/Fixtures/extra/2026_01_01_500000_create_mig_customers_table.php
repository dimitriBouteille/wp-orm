<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */
use Dbout\WpOrm\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class () extends Migration {
    public function up(): void
    {
        $this->schema()->create('mig_customers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('mig_customers');
    }
};
