<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\Unit\Migrations;

use Dbout\WpOrm\Exceptions\MigrationException;
use Dbout\WpOrm\Migrations\MigrationCreator;
use Dbout\WpOrm\Migrations\Migrations;
use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MigrationCreator::class)]
class MigrationCreatorTest extends TestCase
{
    private string $directory;
    private MigrationCreator $creator;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/wp-orm-migration-creator-' . uniqid();
        mkdir($this->directory . '/first', 0777, true);
        mkdir($this->directory . '/second', 0777, true);

        $this->creator = new MigrationCreator(
            new Filesystem(),
            Migrations::STUBS_PATH,
            [$this->directory . '/first', $this->directory . '/second']
        );
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        (new Filesystem())->deleteDirectory($this->directory);
    }

    /**
     * Illuminate rejects a name matching an existing class ("A StdClass class already exists."),
     * the migrations are anonymous classes so the check is pointless.
     *
     * @return void
     */
    public function testNameMatchingExistingClass(): void
    {
        $this->assertTrue(class_exists('StdClass'));

        $file = $this->creator->create('std_class', $this->directory . '/first');

        $this->assertFileExists($file);
    }

    /**
     * Illuminate loads every file of the directory before creating the migration.
     *
     * @return void
     */
    public function testExistingFilesAreNotLoaded(): void
    {
        file_put_contents($this->directory . '/first/broken.php', '<?php throw new \RuntimeException("Loaded.");');

        $file = $this->creator->create('do_something', $this->directory . '/first');

        $this->assertFileExists($file);
    }

    /**
     * @return void
     */
    public function testNameMustBeUniqueAcrossPaths(): void
    {
        $existing = $this->creator->create('create_orders_table', $this->directory . '/second');

        $this->expectException(MigrationException::class);
        $this->expectExceptionMessage(sprintf('A migration named "create_orders_table" already exists: %s', $existing));
        $this->creator->create('create_orders_table', $this->directory . '/first');
    }

    /**
     * @return void
     */
    public function testNameEndingLikeAnotherMigration(): void
    {
        $this->creator->create('create_orders_table', $this->directory . '/first');

        $file = $this->creator->create('orders_table', $this->directory . '/first');

        $this->assertStringEndsWith('_orders_table.php', $file);
        $this->assertCount(2, (array) glob($this->directory . '/first/*.php'));
    }
}
