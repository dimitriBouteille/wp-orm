<?php

namespace Dbout\WpOrm\Migrations\Console\Commands;

class Migration
{
    /**
     * Create database migration with custom name.
     *
     * @param array $args
     * @return void
     */
    public function __invoke(array $args): void
    {
        try {
            $name = $args[0] ?? null;

        } catch (\Exception $exception) {

        }
    }
}