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
        $this->schema()->table('mig_orders', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->index();
        });
    }

    public function down(): void
    {
        $this->schema()->table('mig_orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn('status');
        });
    }
};
