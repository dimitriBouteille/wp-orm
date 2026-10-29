<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\Unit\Migrations;

use Dbout\WpOrm\Exceptions\MigrationException;
use Dbout\WpOrm\Migrations\MigrationRepository;
use Dbout\WpOrm\Migrations\Migrations;
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
}
