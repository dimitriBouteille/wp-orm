<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Migrations\Commands;

use Dbout\WpOrm\Migrations\Commands\MigrateCommand;
use Dbout\WpOrm\Migrations\Commands\RollbackCommand;
use Dbout\WpOrm\Migrations\Migrations;
use Illuminate\Database\Events\MigrationEnded;

class MigrateCommandTest extends CommandTestCase
{
    /**
     * @return void
     * @covers MigrateCommand::__invoke
     * @covers MigrateCommand::withProgress
     */
    public function testMigrate(): void
    {
        $this->enable();

        $this->assertSame(0, $this->runCommand(new MigrateCommand()));

        $this->assertOutputContains('Migration table created.');
        $this->assertOutputContains('Migrating: 2026_01_01_000000_create_mig_orders_table');
        $this->assertOutputContains('Migrated: 2026_01_01_000000_create_mig_orders_table (');
        $this->assertOutputContains('Migrated: 2026_01_02_000000_add_status_to_mig_orders_table (');
        $this->assertOutputContains('Success: 2 migration(s) ran.');
        $this->assertSame('', $this->logger->stderr);

        $this->assertTrue($this->schema->hasColumns('mig_orders', ['id', 'reference', 'status']));
        $this->assertCount(2, $this->getRan());
    }

    /**
     * @return void
     * @covers MigrateCommand::__invoke
     */
    public function testMigrateWithoutPendingMigrations(): void
    {
        $this->enable();
        $this->runCommand(new MigrateCommand());
        $this->logger->stdout = '';

        $this->assertSame(0, $this->runCommand(new MigrateCommand()));

        $this->assertSame("Success: Nothing to migrate.\n", $this->logger->stdout);
    }

    /**
     * @return void
     * @covers MigrateCommand::__invoke
     */
    public function testMigrateWithStep(): void
    {
        $this->enable();

        $this->runCommand(new MigrateCommand(), ['step' => true]);

        $this->assertSame([
            '2026_01_01_000000_create_mig_orders_table' => 1,
            '2026_01_02_000000_add_status_to_mig_orders_table' => 2,
        ], Migrations::getMigrator()->getRepository()->getMigrationBatches());
    }

    /**
     * Progress is printed while migrations run, so the successful ones are visible before the error.
     *
     * @return void
     * @covers MigrateCommand::__invoke
     * @covers MigrateCommand::execute
     */
    public function testFailingMigrationExitsWithError(): void
    {
        $this->enable(['failing']);

        $this->assertSame(1, $this->runCommand(new MigrateCommand()));

        $this->assertOutputContains('Migrated: 2026_02_01_000000_create_mig_customers_table (');
        $this->assertOutputContains('Migrating: 2026_02_02_000000_failing_migration');
        $this->assertErrorOutputContains('Error: Migration failed.');
        $this->assertSame(['2026_02_01_000000_create_mig_customers_table'], $this->getRan());
    }

    /**
     * @return void
     * @covers MigrateCommand::execute
     */
    public function testMissingDirectoryExitsWithError(): void
    {
        Migrations::enable(['paths' => ['/path/does/not/exist'], 'table' => self::TABLE]);

        $this->assertSame(1, $this->runCommand(new MigrateCommand()));

        $this->assertErrorOutputContains('Error: The migration directory "/path/does/not/exist" does not exist.');
    }

    /**
     * @return void
     * @covers MigrateCommand::execute
     */
    public function testMigrationSystemNotEnabled(): void
    {
        $this->assertSame(1, $this->runCommand(new MigrateCommand()));

        $this->assertErrorOutputContains('Error: The migration system is not enabled');
    }

    /**
     * Listeners are removed after the command, running it twice must not duplicate the output.
     *
     * @return void
     * @covers MigrateCommand::withProgress
     */
    public function testProgressListenersAreRemoved(): void
    {
        $this->enable(['basic']);
        $this->runCommand(new MigrateCommand());
        Migrations::getMigrator()->rollback([self::FIXTURES . '/basic']);
        $this->logger->stdout = '';

        $this->runCommand(new MigrateCommand());

        $this->assertSame(1, substr_count($this->logger->stdout, 'Migrating: 2026_01_01_000000_create_mig_orders_table'));
    }

    /**
     * The listeners registered on the public dispatcher must not be removed by the commands.
     *
     * @return void
     * @covers MigrateCommand::withProgress
     */
    public function testUserListenersAreKept(): void
    {
        $this->enable();
        $events = [];
        Migrations::getEventDispatcher()->listen(
            MigrationEnded::class,
            function (MigrationEnded $event) use (&$events): void {
                $events[] = $event->method . ' ' . $event->name;
            }
        );

        $this->runCommand(new MigrateCommand());
        $this->runCommand(new RollbackCommand(), ['yes' => true]);

        $this->assertSame([
            'up 2026_01_01_000000_create_mig_orders_table',
            'up 2026_01_02_000000_add_status_to_mig_orders_table',
            'down 2026_01_02_000000_add_status_to_mig_orders_table',
            'down 2026_01_01_000000_create_mig_orders_table',
        ], $events);
    }

    /**
     * @return void
     * @covers MigrateCommand::withProgress
     */
    public function testProgressIsPrintedOncePerMigration(): void
    {
        $this->enable();

        $this->runCommand(new MigrateCommand());
        $this->runCommand(new RollbackCommand(), ['yes' => true]);
        $this->runCommand(new MigrateCommand());

        $this->assertSame(2, substr_count($this->logger->stdout, 'Migrated: 2026_01_01_000000_create_mig_orders_table ('));
        $this->assertSame(1, substr_count($this->logger->stdout, 'Rolled back: 2026_01_01_000000_create_mig_orders_table ('));
    }

    /**
     * @return void
     * @covers MigrateCommand::withProgress
     */
    public function testNoProgressOutsideCommands(): void
    {
        $this->enable();
        $this->runCommand(new MigrateCommand());
        $this->logger->stdout = '';

        Migrations::getMigrator()->rollback([self::FIXTURES . '/basic']);

        $this->assertSame([], $this->getRan());
        $this->assertSame('', $this->logger->stdout);
    }
}
