<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\Unit\Migrations;

use Dbout\WpOrm\Exceptions\MigrationException;
use Dbout\WpOrm\Migrations\Commands\MakeMigrationCommand;
use Dbout\WpOrm\Migrations\Commands\MigrateCommand;
use Dbout\WpOrm\Migrations\Commands\ResetCommand;
use Dbout\WpOrm\Migrations\Commands\RollbackCommand;
use Dbout\WpOrm\Migrations\Commands\StatusCommand;
use Dbout\WpOrm\Migrations\MigrationRepository;
use Dbout\WpOrm\Migrations\Migrations;
use Dbout\WpOrm\Migrations\Migrator;
use Illuminate\Database\Events\NoPendingMigrations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
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
            $this->fail('An invalid configuration must throw an exception.');
        } catch (MigrationException $exception) {
            $this->assertSame('The "paths" option must be a non-empty list of directories.', $exception->getMessage());
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

        $this->assertInstanceOf(Migrator::class, $migrator);
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
            'orm make:migration' => MakeMigrationCommand::class,
        ], Migrations::COMMANDS);
    }

    /**
     * WP-CLI handles its global options itself (--path, --url, --user...), they never reach the command.
     *
     * @return void
     */
    public function testCommandsDoNotUseWpCliGlobalOptions(): void
    {
        $spec = require dirname(__DIR__, 3) . '/vendor/wp-cli/wp-cli/php/config-spec.php';
        $globalOptions = array_keys(array_filter($spec, static fn (array $option): bool => ($option['runtime'] ?? false) !== false));
        $this->assertContains('path', $globalOptions);

        foreach (Migrations::COMMANDS as $name => $command) {
            $docComment = (string) (new \ReflectionMethod($command, '__invoke'))->getDocComment();
            preg_match_all('/^\s*\* \[?--([\w-]+)/m', $docComment, $matches);

            $this->assertSame([], array_intersect($matches[1], $globalOptions), $name);
        }
    }

    /**
     * @return void
     */
    public function testGetMigrationCreatorWhenDisabled(): void
    {
        $this->expectException(MigrationException::class);
        $this->expectExceptionMessage('The migration system is not enabled, call Migrations::enable() first.');
        Migrations::getMigrationCreator();
    }

    /**
     * The package stubs must be used, not the Illuminate ones (Schema facade, not available in WordPress).
     *
     * @param string $stub
     * @return void
     */
    #[TestWith(['migration.stub'])]
    #[TestWith(['migration.create.stub'])]
    #[TestWith(['migration.update.stub'])]
    public function testStubs(string $stub): void
    {
        Migrations::enable(['paths' => [__DIR__]]);
        Migrations::getMigrationCreator();

        $content = (string) file_get_contents(Migrations::STUBS_PATH . '/' . $stub);
        $this->assertStringContainsString('use Dbout\WpOrm\Migrations\Migration;', $content);
        $this->assertStringNotContainsString('Schema::', $content);
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
