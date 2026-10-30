<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\Unit\Migrations;

use Dbout\WpOrm\Exceptions\MigrationException;
use Dbout\WpOrm\Migrations\Commands\MigrateCommand;
use Dbout\WpOrm\Migrations\Commands\ResetCommand;
use Dbout\WpOrm\Migrations\Commands\RollbackCommand;
use Dbout\WpOrm\Migrations\Commands\StatusCommand;
use Dbout\WpOrm\Migrations\MigrationRepository;
use Dbout\WpOrm\Migrations\Migrations;
use Illuminate\Database\Events\NoPendingMigrations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Migrations::class)]
class MigrationsTest extends TestCase
{
    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        Migrations::reset();
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        Migrations::reset();
    }

    /**
     * @return void
     */
    public function testDisabledByDefault(): void
    {
        $this->assertFalse(Migrations::isEnabled());
    }

    /**
     * @return void
     */
    public function testEnable(): void
    {
        Migrations::enable(['paths' => [__DIR__], 'table' => 'app_migrations']);

        $this->assertTrue(Migrations::isEnabled());
        $this->assertSame([__DIR__], Migrations::getConfig()->paths);
        $this->assertSame('app_migrations', Migrations::getConfig()->table);
    }

    /**
     * @return void
     */
    public function testEnableTwiceThrows(): void
    {
        Migrations::enable(['paths' => [__DIR__]]);

        $this->expectException(MigrationException::class);
        $this->expectExceptionMessage('The migration system is already enabled.');
        Migrations::enable(['paths' => [__DIR__]]);
    }

    /**
     * @return void
     */
    public function testEnableWithInvalidConfigKeepsSystemDisabled(): void
    {
        try {
            Migrations::enable(['paths' => []]);
        } catch (MigrationException) {
        }

        $this->assertFalse(Migrations::isEnabled());
    }

    /**
     * @return void
     */
    public function testEnableDoesNotCheckDirectories(): void
    {
        Migrations::enable(['paths' => ['/path/does/not/exist']]);

        $this->assertTrue(Migrations::isEnabled());
    }

    /**
     * @return void
     */
    public function testGetConfigWhenDisabled(): void
    {
        $this->expectException(MigrationException::class);
        $this->expectExceptionMessage('The migration system is not enabled, call Migrations::enable() first.');
        Migrations::getConfig();
    }

    /**
     * @return void
     */
    public function testGetMigratorWhenDisabled(): void
    {
        $this->expectException(MigrationException::class);
        $this->expectExceptionMessage('The migration system is not enabled, call Migrations::enable() first.');
        Migrations::getMigrator();
    }

    /**
     * @return void
     */
    public function testGetMigratorWithMissingDirectory(): void
    {
        Migrations::enable(['paths' => [__DIR__, '/path/does/not/exist']]);

        $this->expectException(MigrationException::class);
        $this->expectExceptionMessage('The migration directory "/path/does/not/exist" does not exist.');
        Migrations::getMigrator();
    }

    /**
     * @return void
     */
    public function testGetMigrator(): void
    {
        Migrations::enable(['paths' => [__DIR__]]);
        $migrator = Migrations::getMigrator();

        $this->assertInstanceOf(MigrationRepository::class, $migrator->getRepository());
        $this->assertSame($migrator, Migrations::getMigrator());
    }

    /**
     * @return void
     */
    public function testResetDisablesTheSystem(): void
    {
        Migrations::enable(['paths' => [__DIR__]]);
        $migrator = Migrations::getMigrator();

        Migrations::reset();
        $this->assertFalse(Migrations::isEnabled());

        Migrations::enable(['paths' => [__DIR__]]);
        $this->assertNotSame($migrator, Migrations::getMigrator());
    }

    /**
     * @return void
     */
    public function testCommands(): void
    {
        $this->assertSame([
            'orm migrate' => MigrateCommand::class,
            'orm migrate:rollback' => RollbackCommand::class,
            'orm migrate:status' => StatusCommand::class,
            'orm migrate:reset' => ResetCommand::class,
        ], Migrations::COMMANDS);
    }

    /**
     * WP-CLI is not loaded in the unit tests: enable() must not try to register the commands.
     *
     * @return void
     */
    public function testEnableOutsideWpCli(): void
    {
        $this->assertFalse(defined('WP_CLI'));

        Migrations::enable(['paths' => [__DIR__]]);

        $this->assertTrue(Migrations::isEnabled());
    }

    /**
     * @return void
     */
    public function testEventDispatcher(): void
    {
        $events = Migrations::getEventDispatcher();
        $this->assertSame($events, Migrations::getEventDispatcher());

        Migrations::reset();
        $this->assertNotSame($events, Migrations::getEventDispatcher());
    }

    /**
     * @return void
     */
    public function testMigratorDispatchesEvents(): void
    {
        Migrations::enable(['paths' => [__DIR__]]);
        $dispatched = [];
        Migrations::getEventDispatcher()->listen(
            NoPendingMigrations::class,
            function (NoPendingMigrations $event) use (&$dispatched): void {
                $dispatched[] = $event->method;
            }
        );

        Migrations::getMigrator()->fireMigrationEvent(new NoPendingMigrations('up'));

        $this->assertSame(['up'], $dispatched);
    }
}
