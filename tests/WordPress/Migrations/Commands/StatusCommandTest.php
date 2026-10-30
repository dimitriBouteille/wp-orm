<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Migrations\Commands;

use Dbout\WpOrm\Migrations\Commands\MigrateCommand;
use Dbout\WpOrm\Migrations\Commands\StatusCommand;
use Dbout\WpOrm\Migrations\Migrations;

class StatusCommandTest extends CommandTestCase
{
    /**
     * @return void
     * @covers StatusCommand::__invoke
     * @covers StatusCommand::getRows
     */
    public function testStatus(): void
    {
        $this->enable(['basic', 'extra']);
        $this->runCommand(new MigrateCommand(), ['step' => true]);
        Migrations::getMigrator()->rollback([self::FIXTURES . '/basic', self::FIXTURES . '/extra'], ['step' => 1]);

        $this->assertSame([
            ['migration' => '2026_01_01_000000_create_mig_orders_table', 'batch' => 1, 'status' => 'Ran'],
            ['migration' => '2026_01_01_500000_create_mig_customers_table', 'batch' => 2, 'status' => 'Ran'],
            ['migration' => '2026_01_02_000000_add_status_to_mig_orders_table', 'batch' => null, 'status' => 'Pending'],
        ], $this->getStatusRows());
    }

    /**
     * @return void
     * @covers StatusCommand::__invoke
     */
    public function testWithoutMigrationTable(): void
    {
        $this->enable();

        $this->assertSame([
            ['migration' => '2026_01_01_000000_create_mig_orders_table', 'batch' => null, 'status' => 'Pending'],
            ['migration' => '2026_01_02_000000_add_status_to_mig_orders_table', 'batch' => null, 'status' => 'Pending'],
        ], $this->getStatusRows());
        $this->assertErrorOutputContains('Warning: Migration table not found, run "wp orm migrate" to create it.');
        $this->assertFalse($this->schema->hasTable(self::TABLE));
    }

    /**
     * @return void
     * @covers StatusCommand::__invoke
     */
    public function testWithoutMigrationFiles(): void
    {
        Migrations::enable(['paths' => [__DIR__], 'table' => self::TABLE]);

        $this->assertSame(0, $this->runCommand(new StatusCommand()));

        $this->assertErrorOutputContains('Warning: No migrations found.');
    }

    /**
     * @return array<array{migration: string, batch: int|null, status: string}>
     */
    private function getStatusRows(): array
    {
        ob_start();
        try {
            $exitCode = $this->runCommand(new StatusCommand(), ['format' => 'json']);
        } finally {
            $output = (string) ob_get_clean();
        }

        $this->assertSame(0, $exitCode);

        return json_decode($output, true, 512, JSON_THROW_ON_ERROR);
    }
}
