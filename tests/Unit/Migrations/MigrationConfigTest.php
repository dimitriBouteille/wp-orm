<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\Unit\Migrations;

use Dbout\WpOrm\Exceptions\MigrationException;
use Dbout\WpOrm\Migrations\MigrationConfig;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(MigrationConfig::class)]
class MigrationConfigTest extends TestCase
{
    /**
     * @return void
     */
    public function testFromArrayWithDefaultTable(): void
    {
        $config = MigrationConfig::fromArray(['paths' => ['/app/migrations']]);

        $this->assertSame(['/app/migrations'], $config->paths);
        $this->assertSame(MigrationConfig::DEFAULT_TABLE, $config->table);
    }

    /**
     * @return void
     */
    public function testFromArrayWithCustomTable(): void
    {
        $config = MigrationConfig::fromArray([
            'paths' => ['/app/migrations', '/app/plugin/migrations'],
            'table' => 'app_migrations',
        ]);

        $this->assertSame(['/app/migrations', '/app/plugin/migrations'], $config->paths);
        $this->assertSame('app_migrations', $config->table);
    }

    /**
     * @param string $table
     * @return void
     */
    #[DataProvider('providerValidTableName')]
    public function testValidTableName(string $table): void
    {
        $config = new MigrationConfig(['/app/migrations'], $table);

        $this->assertSame($table, $config->table);
    }

    /**
     * @return \Generator
     */
    public static function providerValidTableName(): \Generator
    {
        yield 'Lowercase' => ['migrations'];
        yield 'Uppercase and digits' => ['App_Migrations_2'];
        yield 'Single character' => ['m'];
        yield '64 characters' => [str_repeat('m', 64)];
    }

    /**
     * @param string $table
     * @return void
     */
    #[DataProvider('providerInvalidTableName')]
    public function testInvalidTableName(string $table): void
    {
        $this->expectException(MigrationException::class);
        $this->expectExceptionMessage(sprintf(
            'Invalid migrations table name "%s", only letters, numbers and underscores are allowed (64 characters max).',
            $table
        ));

        new MigrationConfig(['/app/migrations'], $table);
    }

    /**
     * @return \Generator
     */
    public static function providerInvalidTableName(): \Generator
    {
        yield 'Empty' => [''];
        yield 'Hyphen' => ['wp-migrations'];
        yield 'Space' => ['wp migrations'];
        yield 'Dot' => ['db.migrations'];
        yield 'Backtick' => ['migrations`'];
        yield 'Accent' => ['migrätions'];
        yield 'Trailing new line' => ["migrations\n"];
        yield '65 characters' => [str_repeat('m', 65)];
    }

    /**
     * @param array<string, mixed> $config
     * @param string $message
     * @return void
     */
    #[DataProvider('providerInvalidConfig')]
    public function testInvalidConfig(array $config, string $message): void
    {
        $this->expectException(MigrationException::class);
        $this->expectExceptionMessage($message);

        MigrationConfig::fromArray($config);
    }

    /**
     * @return \Generator
     */
    public static function providerInvalidConfig(): \Generator
    {
        yield 'Missing paths' => [
            [],
            'The "paths" option is required and must be an array.',
        ];

        yield 'Paths is a string' => [
            ['paths' => '/app/migrations'],
            'The "paths" option is required and must be an array.',
        ];

        yield 'Empty paths' => [
            ['paths' => []],
            'The "paths" option must be a non-empty list of directories.',
        ];

        yield 'Associative paths' => [
            ['paths' => ['app' => '/app/migrations']],
            'The "paths" option must be a non-empty list of directories.',
        ];

        yield 'Empty path' => [
            ['paths' => ['  ']],
            'Each migration path must be a non-empty string.',
        ];

        yield 'Path is not a string' => [
            ['paths' => [12]],
            'Each migration path must be a non-empty string.',
        ];

        yield 'Table is not a string' => [
            ['paths' => ['/app/migrations'], 'table' => 12],
            'The "table" option must be a string.',
        ];

        yield 'Invalid table name' => [
            ['paths' => ['/app/migrations'], 'table' => 'wp-migrations'],
            'Invalid migrations table name "wp-migrations", only letters, numbers and underscores are allowed (64 characters max).',
        ];

        yield 'Unknown option' => [
            ['paths' => ['/app/migrations'], 'path' => '/app'],
            'Unknown migration option(s): path.',
        ];
    }
}
