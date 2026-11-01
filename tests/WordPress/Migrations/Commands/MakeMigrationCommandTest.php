<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Migrations\Commands;

use Dbout\WpOrm\Migrations\Commands\MakeMigrationCommand;
use Dbout\WpOrm\Migrations\Commands\MigrateCommand;
use Dbout\WpOrm\Migrations\Commands\RollbackCommand;
use Dbout\WpOrm\Migrations\Migrations;
use Illuminate\Filesystem\Filesystem;

class MakeMigrationCommandTest extends CommandTestCase
{
    private const string GENERATED_TABLE = 'mig_generated';

    private string $directory;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/wp-orm-make-migration-' . uniqid();
        mkdir($this->directory . '/first', 0777, true);
        mkdir($this->directory . '/second', 0777, true);

        Migrations::enable([
            'paths' => [$this->directory . '/first', $this->directory . '/second'],
            'table' => self::TABLE,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        (new Filesystem())->deleteDirectory($this->directory);
        $this->schema->dropIfExists(self::GENERATED_TABLE);

        parent::tearDown();
    }

    /**
     * @return void
     * @covers MakeMigrationCommand::__invoke
     */
    public function testMakeBlankMigration(): void
    {
        $this->assertSame(0, $this->make('do_something'));

        $file = $this->getCreatedFile();
        $this->assertSame($this->directory . '/first', dirname($file));
        $this->assertMatchesRegularExpression('/^\d{4}_\d{2}_\d{2}_\d{6}_do_something\.php$/', basename($file));
        $this->assertOutputContains('Success: Migration created: ' . $file);

        $content = (string) file_get_contents($file);
        $this->assertStringContainsString('return new class () extends Migration {', $content);
        $this->assertStringContainsString('use Dbout\WpOrm\Migrations\Migration;', $content);
        $this->assertStringNotContainsString('$this->schema()', $content);
    }

    /**
     * @param string $name
     * @param array<string, string> $options
     * @param string $expected
     * @return void
     * @covers MakeMigrationCommand::getTable
     * @dataProvider providerTable
     */
    public function testTable(string $name, array $options, string $expected): void
    {
        $this->assertSame(0, $this->make($name, $options));

        $this->assertStringContainsString($expected, (string) file_get_contents($this->getCreatedFile()));
    }

    /**
     * @return \Generator<string, array{string, array<string, string>, string}>
     */
    public static function providerTable(): \Generator
    {
        yield 'Guess create' => ['create_orders_table', [], "\$this->schema()->create('orders', function (Blueprint \$table): void {"];
        yield 'Guess create without suffix' => ['create_orders', [], "\$this->schema()->dropIfExists('orders');"];
        yield 'Guess update' => ['add_status_to_orders_table', [], "\$this->schema()->table('orders', function (Blueprint \$table): void {"];
        yield 'Create option' => ['init_shop', ['create' => 'shop_orders'], "\$this->schema()->create('shop_orders', "];
        yield 'Create option wins over guess' => ['create_orders_table', ['create' => 'shop_orders'], "\$this->schema()->create('shop_orders', "];
        yield 'Table option' => ['change_shop', ['table' => 'shop_orders'], "\$this->schema()->table('shop_orders', "];
    }

    /**
     * @return void
     * @covers MakeMigrationCommand::__invoke
     */
    public function testNameIsConvertedToSnakeCase(): void
    {
        $this->assertSame(0, $this->make('CreateOrdersTable'));

        $this->assertStringEndsWith('_create_orders_table.php', $this->getCreatedFile());
    }

    /**
     * @param string $name
     * @return void
     * @covers MakeMigrationCommand::__invoke
     * @testWith ["../evil"]
     *           ["create-orders"]
     *           ["création"]
     *           [" "]
     */
    public function testInvalidName(string $name): void
    {
        $this->assertSame(1, $this->make($name));

        $this->assertErrorOutputContains(sprintf(
            'Error: Invalid migration name "%s", only letters, numbers and underscores are allowed.',
            $name
        ));
        $this->assertNoFileCreated();
    }

    /**
     * @param string $option
     * @param string $value
     * @return void
     * @covers MakeMigrationCommand::getTableOption
     * @testWith ["create", "orders'); exit; //"]
     *           ["table", "shop-orders"]
     *           ["create", ""]
     *           ["table", "a_table_name_that_is_way_too_long_for_mysql_a_table_name_that_is_"]
     */
    public function testInvalidTableOption(string $option, string $value): void
    {
        $this->assertSame(1, $this->make('do_something', [$option => $value]));

        $this->assertErrorOutputContains(sprintf(
            'Error: The --%s option must be a table name, only letters, numbers and underscores are allowed.',
            $option
        ));
        $this->assertNoFileCreated();
    }

    /**
     * WP-CLI gives true for an option without value (--create).
     *
     * @return void
     * @covers MakeMigrationCommand::getTableOption
     */
    public function testTableOptionWithoutValue(): void
    {
        $this->assertSame(1, $this->make('do_something', ['create' => true]));

        $this->assertErrorOutputContains('Error: The --create option must be a table name');
    }

    /**
     * @return void
     * @covers MakeMigrationCommand::getTable
     */
    public function testCreateAndTableCannotBeUsedTogether(): void
    {
        $this->assertSame(1, $this->make('do_something', ['create' => 'orders', 'table' => 'orders']));

        $this->assertErrorOutputContains('Error: The --create and --table options cannot be used together.');
        $this->assertNoFileCreated();
    }

    /**
     * @param string $path
     * @return void
     * @covers MakeMigrationCommand::getDirectory
     * @testWith ["/second"]
     *           ["/second/"]
     *           ["/first/../second"]
     */
    public function testDirOption(string $path): void
    {
        $this->assertSame(0, $this->make('do_something', ['dir' => $this->directory . $path]));

        $this->assertSame($this->directory . '/second', dirname($this->getCreatedFile()));
    }

    /**
     * @return void
     * @covers MakeMigrationCommand::getDirectory
     */
    public function testDirOptionMustBeConfigured(): void
    {
        $this->assertSame(1, $this->make('do_something', ['dir' => $this->directory]));

        $this->assertErrorOutputContains(sprintf(
            'Error: The directory "%1$s" is not a configured migration path, available paths: %1$s/first, %1$s/second',
            $this->directory
        ));
        $this->assertNoFileCreated();
    }

    /**
     * The configured directory is created on the first migration.
     *
     * @return void
     * @covers MakeMigrationCommand::__invoke
     */
    public function testMissingDirectoryIsCreated(): void
    {
        rmdir($this->directory . '/first');

        $this->assertSame(0, $this->make('do_something'));

        $this->assertSame($this->directory . '/first', dirname($this->getCreatedFile()));
    }

    /**
     * @return void
     * @covers \Dbout\WpOrm\Migrations\MigrationCreator::ensureMigrationDoesntAlreadyExist
     */
    public function testNameMustBeUniqueAcrossPaths(): void
    {
        $this->make('create_orders_table', ['dir' => $this->directory . '/second']);
        $existing = $this->getCreatedFile();

        $this->assertSame(1, $this->make('create_orders_table'));
        $this->assertSame(0, $this->make('orders_table'));

        $this->assertErrorOutputContains(sprintf(
            'Error: A migration named "create_orders_table" already exists: %s',
            $existing
        ));
        $this->assertCount(1, (array) glob($this->directory . '/first/*.php'));
    }

    /**
     * Illuminate rejects a name matching an existing class, WordPress has a Walker class.
     *
     * @return void
     * @covers \Dbout\WpOrm\Migrations\MigrationCreator::ensureMigrationDoesntAlreadyExist
     */
    public function testNameMatchingWordPressClass(): void
    {
        $this->assertTrue(class_exists('Walker'));

        $this->assertSame(0, $this->make('walker'));

        $this->assertStringEndsWith('_walker.php', $this->getCreatedFile());
    }

    /**
     * The generated migrations must run as they are.
     *
     * @return void
     * @covers MakeMigrationCommand::__invoke
     */
    public function testGeneratedMigrationsRun(): void
    {
        $this->make('create_' . self::GENERATED_TABLE . '_table');
        $this->make('update_' . self::GENERATED_TABLE, ['table' => self::GENERATED_TABLE]);
        $this->make('do_something', ['dir' => $this->directory . '/second']);

        $this->assertSame(0, $this->runCommand(new MigrateCommand()));
        $this->assertOutputContains('Success: 3 migration(s) ran.');
        $this->assertTrue($this->schema->hasColumns(self::GENERATED_TABLE, ['id', 'created_at', 'updated_at']));

        $this->assertSame(0, $this->runCommand(new RollbackCommand(), ['yes' => true]));
        $this->assertOutputContains('Success: 3 migration(s) rolled back.');
        $this->assertFalse($this->schema->hasTable(self::GENERATED_TABLE));
    }

    /**
     * @param string $name
     * @param array<string, mixed> $options
     * @return int Exit code
     */
    private function make(string $name, array $options = []): int
    {
        return $this->runCommand(new MakeMigrationCommand(), $options, [$name]);
    }

    /**
     * @return string
     */
    private function getCreatedFile(): string
    {
        $files = array_merge(
            (array) glob($this->directory . '/first/*.php'),
            (array) glob($this->directory . '/second/*.php')
        );
        $this->assertCount(1, $files);

        return (string) $files[0];
    }

    /**
     * @return void
     */
    private function assertNoFileCreated(): void
    {
        $this->assertSame([], (array) glob($this->directory . '/*/*.php'));
    }
}
