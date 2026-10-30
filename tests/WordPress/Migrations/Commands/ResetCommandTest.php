<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Migrations\Commands;

use Dbout\WpOrm\Migrations\Commands\MigrateCommand;
use Dbout\WpOrm\Migrations\Commands\ResetCommand;
use WP_CLI\ExitException;

class ResetCommandTest extends CommandTestCase
{
    /**
     * @return void
     * @covers ResetCommand::__invoke
     */
    public function testReset(): void
    {
        $this->enable(['basic', 'extra']);
        $this->runCommand(new MigrateCommand(), ['step' => true]);
        $this->logger->stdout = '';

        $this->assertSame(0, $this->runCommand(new ResetCommand(), ['yes' => true]));

        $this->assertOutputContains('Rolled back: 2026_01_02_000000_add_status_to_mig_orders_table (');
        $this->assertOutputContains('Rolled back: 2026_01_01_500000_create_mig_customers_table (');
        $this->assertOutputContains('Rolled back: 2026_01_01_000000_create_mig_orders_table (');
        $this->assertOutputContains('Success: 3 migration(s) rolled back.');
        $this->assertSame([], $this->getRan());
        $this->assertFalse($this->schema->hasTable('mig_orders'));
        $this->assertTrue($this->schema->hasTable(self::TABLE));
    }

    /**
     * @return void
     * @covers ResetCommand::__invoke
     */
    public function testWithoutMigrationTable(): void
    {
        $this->enable();

        $this->assertSame(0, $this->runCommand(new ResetCommand(), ['yes' => true]));

        $this->assertErrorOutputContains('Warning: Migration table not found, nothing to reset.');
    }

    /**
     * @return void
     * @covers ResetCommand::__invoke
     */
    public function testNothingToRollBack(): void
    {
        $this->enable();
        $this->runCommand(new MigrateCommand());
        $this->runCommand(new ResetCommand(), ['yes' => true]);
        $this->logger->stdout = '';

        $this->assertSame(0, $this->runCommand(new ResetCommand(), ['yes' => true]));

        $this->assertOutputContains('Success: Nothing to roll back.');
    }

    /**
     * @return void
     * @covers ResetCommand::confirm
     */
    public function testDeclinedConfirmation(): void
    {
        $this->enable();
        $this->runCommand(new MigrateCommand());

        $command = new class () extends ResetCommand {
            public ?string $question = null;

            protected function confirm(string $question, array $assocArgs): void
            {
                $this->question = $question;
                // WP_CLI::confirm() exits with code 0 when the answer is not "y".
                throw new ExitException('', 0);
            }
        };

        $this->assertSame(0, $this->runCommand($command));

        $this->assertSame('Are you sure you want to roll back all the migrations?', $command->question);
        $this->assertCount(2, $this->getRan());
    }
}
