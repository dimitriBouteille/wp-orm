<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Migrations\Commands;

use Dbout\WpOrm\Migrations\Commands\RollbackCommand;
use Dbout\WpOrm\Migrations\Migrations;
use WP_CLI\ExitException;

class RollbackCommandTest extends CommandTestCase
{
    /**
     * @return void
     * @covers RollbackCommand::__invoke
     */
    public function testRollbackLastBatch(): void
    {
        $this->enable(['basic', 'extra']);
        $this->migrate(['basic']);
        $this->migrate(['basic', 'extra']);

        $this->assertSame(0, $this->runCommand(new RollbackCommand(), ['yes' => true]));

        $this->assertOutputContains('Rolling back: 2026_01_01_500000_create_mig_customers_table');
        $this->assertOutputContains('Rolled back: 2026_01_01_500000_create_mig_customers_table (');
        $this->assertOutputContains('Success: 1 migration(s) rolled back.');
        $this->assertFalse($this->schema->hasTable('mig_customers'));
        $this->assertCount(2, $this->getRan());
    }

    /**
     * @return void
     * @covers RollbackCommand::__invoke
     */
    public function testRollbackStep(): void
    {
        $this->enable();
        $this->migrate(['basic']);

        $this->runCommand(new RollbackCommand(), ['step' => '1', 'yes' => true]);

        $this->assertSame(['2026_01_01_000000_create_mig_orders_table'], $this->getRan());
        $this->assertFalse($this->schema->hasColumn('mig_orders', 'status'));
    }

    /**
     * @return void
     * @covers RollbackCommand::__invoke
     */
    public function testRollbackBatch(): void
    {
        $this->enable(['basic', 'extra']);
        $this->migrate(['extra']);
        $this->migrate(['basic', 'extra']);

        $this->runCommand(new RollbackCommand(), ['batch' => '1', 'yes' => true]);

        $this->assertSame([
            '2026_01_01_000000_create_mig_orders_table',
            '2026_01_02_000000_add_status_to_mig_orders_table',
        ], $this->getRan());
        $this->assertFalse($this->schema->hasTable('mig_customers'));
    }

    /**
     * @return void
     * @covers RollbackCommand::__invoke
     */
    public function testStepAndBatchCannotBeUsedTogether(): void
    {
        $this->enable();

        $this->assertSame(1, $this->runCommand(new RollbackCommand(), ['step' => '1', 'batch' => '1', 'yes' => true]));

        $this->assertErrorOutputContains('Error: The --step and --batch options cannot be used together.');
    }

    /**
     * @param string $option
     * @param string $value
     * @return void
     * @covers RollbackCommand::getPositiveInt
     * @testWith ["step", "0"]
     *           ["step", "-1"]
     *           ["step", "abc"]
     *           ["batch", "1.5"]
     */
    public function testInvalidOption(string $option, string $value): void
    {
        $this->enable();

        $this->assertSame(1, $this->runCommand(new RollbackCommand(), [$option => $value, 'yes' => true]));

        $this->assertErrorOutputContains(sprintf('Error: The --%s option must be a positive integer.', $option));
    }

    /**
     * @return void
     * @covers RollbackCommand::__invoke
     */
    public function testWithoutMigrationTable(): void
    {
        $this->enable();

        $this->assertSame(0, $this->runCommand(new RollbackCommand(), ['yes' => true]));

        $this->assertErrorOutputContains('Warning: Migration table not found, nothing to roll back.');
        $this->assertFalse($this->schema->hasTable(self::TABLE));
    }

    /**
     * @return void
     * @covers RollbackCommand::__invoke
     */
    public function testNothingToRollBack(): void
    {
        $this->enable();
        Migrations::getMigrator()->getRepository()->createRepository();

        $this->assertSame(0, $this->runCommand(new RollbackCommand(), ['yes' => true]));

        $this->assertOutputContains('Success: Nothing to roll back.');
    }

    /**
     * @return void
     * @covers RollbackCommand::confirm
     */
    public function testDeclinedConfirmation(): void
    {
        $this->enable();
        $this->migrate(['basic']);

        $command = new class () extends RollbackCommand {
            public ?string $question = null;

            protected function confirm(string $question, array $assocArgs): void
            {
                $this->question = $question;
                // WP_CLI::confirm() exits with code 0 when the answer is not "y".
                throw new ExitException('', 0);
            }
        };

        $this->assertSame(0, $this->runCommand($command, ['step' => '2']));

        $this->assertSame('Are you sure you want to roll back the last 2 migration(s)?', $command->question);
        $this->assertCount(2, $this->getRan());
    }

    /**
     * @param array<string> $directories
     * @return void
     */
    private function migrate(array $directories): void
    {
        $migrator = Migrations::getMigrator();
        if (!$migrator->repositoryExists()) {
            $migrator->getRepository()->createRepository();
        }

        $migrator->run(array_map(
            static fn (string $directory): string => self::FIXTURES . '/' . $directory,
            $directories
        ));
    }
}
