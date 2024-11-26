<?php

namespace Dbout\WpOrm\Migrations;

use Illuminate\Database\Schema\Builder;

interface MigrationInterface
{
    public function up(Builder $builder): void;

    /**
     * @param Builder $builder
     * @return void
     */
    public function down(Builder $builder): void;
}
