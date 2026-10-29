<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Migrations;

use Dbout\WpOrm\Migrations\MigrationRepository;
use Dbout\WpOrm\Migrations\Migrations;
use Dbout\WpOrm\Orm\Database;
use Dbout\WpOrm\Orm\Schemas\WordPressBuilder;
use Dbout\WpOrm\Tests\WordPress\TestCase;
use Illuminate\Database\Migrations\Migrator;

/**
 * Runs Illuminate's migrator on top of the wpdb connection.
 *
 * Migrations execute real DDL statements, which implicitly commit the transaction
 * opened by the WordPress test suite: every table created here is dropped in tearDown().
 */
class MigratorTest extends TestCase
{
    private const string FIXTURES = __DIR__ . '/Fixtures';
    private const array TABLES = ['mig_orders', 'mig_customers', 'mig_partial', 'test_migrations', 'migrations'];

    private WordPressBuilder $schema;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->schema = Database::getInstance()->getSchemaBuilder();
        Migrations::reset();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        Migrations::reset();
        foreach (self::TABLES as $table) {
            $this->schema->dropIfExists($table);
        }

        parent::tearDown();
    }

    /**
     * @return void
     * @covers Migrations::getMigrator
     * @covers MigrationRepository::createRepository
     */
    public function testMigrationsTableIsPrefixed(): void
    {
        global $wpdb;
        $migrator = $this->createMigrator(['basic'], 'test_migrations');

        $this->assertTrue($migrator->repositoryExists());
        $this->assertSame(
            $wpdb->prefix . 'test_migrations',
            $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->prefix . 'test_migrations'))
        );
    }

    /**
     * @return void
     * @covers Migrations::getMigrator
     */
    public function testDefaultMigrationsTable(): void
    {
        Migrations::enable(['paths' => [self::FIXTURES . '/basic']]);
        $this->repository(Migrations::getMigrator())->createRepository();

        $this->assertTrue($this->schema->hasTable('migrations'));
    }

    /**
     * @return void
     * @covers Migrations::getMigrator
     * @covers MigrationRepository::getLastBatchNumber
     */
    public function testRunPendingMigrations(): void
    {
        $migrator = $this->createMigrator(['basic']);
        $ran = $migrator->run($this->paths(['basic']));

        $this->assertCount(2, $ran);
        $this->assertSame([
            '2026_01_01_000000_create_mig_orders_table',
            '2026_01_02_000000_add_status_to_mig_orders_table',
        ], $this->repository($migrator)->getRan());
        $this->assertSame(1, $this->repository($migrator)->getLastBatchNumber());
        $this->assertTrue($this->schema->hasColumns('mig_orders', ['id', 'reference', 'post_id', 'status']));
        $this->assertTrue($this->schema->hasIndex('mig_orders', ['status']));
    }

    /**
     * @return void
     * @covers Migrations::getMigrator
     */
    public function testRunWithoutPendingMigrations(): void
    {
        $migrator = $this->createMigrator(['basic']);
        $migrator->run($this->paths(['basic']));

        $this->assertSame([], $migrator->run($this->paths(['basic'])));
        $this->assertSame(1, $this->repository($migrator)->getLastBatchNumber());
    }

    /**
     * @return void
     * @covers MigrationRepository::getMigrationBatches
     */
    public function testRunWithStep(): void
    {
        $migrator = $this->createMigrator(['basic']);
        $migrator->run($this->paths(['basic']), ['step' => true]);

        $this->assertSame([
            '2026_01_01_000000_create_mig_orders_table' => 1,
            '2026_01_02_000000_add_status_to_mig_orders_table' => 2,
        ], $this->repository($migrator)->getMigrationBatches());
    }

    /**
     * @return void
     * @covers Migrations::getMigrator
     */
    public function testMigrationsFromSeveralPathsRunInChronologicalOrder(): void
    {
        $migrator = $this->createMigrator(['basic', 'extra']);
        $migrator->run($this->paths(['basic', 'extra']));

        $this->assertSame([
            '2026_01_01_000000_create_mig_orders_table',
            '2026_01_01_500000_create_mig_customers_table',
            '2026_01_02_000000_add_status_to_mig_orders_table',
        ], $this->repository($migrator)->getRan());
        $this->assertTrue($this->schema->hasTable('mig_customers'));
    }

    /**
     * @return void
     * @covers MigrationRepository::getLast
     */
    public function testRollbackLastBatch(): void
    {
        $migrator = $this->createMigrator(['basic', 'extra']);
        $migrator->run($this->paths(['basic']));
        $migrator->run($this->paths(['basic', 'extra']));

        $rolledBack = $migrator->rollback($this->paths(['basic', 'extra']));

        $this->assertCount(1, $rolledBack);
        $this->assertFalse($this->schema->hasTable('mig_customers'));
        $this->assertTrue($this->schema->hasTable('mig_orders'));
        $this->assertSame(1, $this->repository($migrator)->getLastBatchNumber());
    }

    /**
     * @return void
     * @covers MigrationRepository::getMigrations
     */
    public function testRollbackSteps(): void
    {
        $migrator = $this->createMigrator(['basic']);
        $migrator->run($this->paths(['basic']));

        $migrator->rollback($this->paths(['basic']), ['step' => 1]);

        $this->assertSame(['2026_01_01_000000_create_mig_orders_table'], $this->repository($migrator)->getRan());
        $this->assertFalse($this->schema->hasColumn('mig_orders', 'status'));
    }

    /**
     * @return void
     * @covers MigrationRepository::getMigrationsByBatch
     */
    public function testRollbackBatch(): void
    {
        $migrator = $this->createMigrator(['basic', 'extra']);
        $migrator->run($this->paths(['extra']));
        $migrator->run($this->paths(['basic', 'extra']));

        $migrator->rollback($this->paths(['basic', 'extra']), ['batch' => 1]);

        $this->assertSame([
            '2026_01_01_000000_create_mig_orders_table',
            '2026_01_02_000000_add_status_to_mig_orders_table',
        ], $this->repository($migrator)->getRan());
        $this->assertFalse($this->schema->hasTable('mig_customers'));
    }

    /**
     * @return void
     * @covers Migrations::getMigrator
     */
    public function testReset(): void
    {
        $migrator = $this->createMigrator(['basic', 'extra']);
        $migrator->run($this->paths(['basic']), ['step' => true]);
        $migrator->run($this->paths(['basic', 'extra']));

        $migrator->reset($this->paths(['basic', 'extra']));

        $this->assertSame([], $this->repository($migrator)->getRan());
        $this->assertFalse($this->schema->hasTable('mig_orders'));
        $this->assertFalse($this->schema->hasTable('mig_customers'));
        $this->assertTrue($migrator->repositoryExists());
    }

    /**
     * MySQL does not support transactional DDL: the statements executed before the
     * failure are kept, but the failed migration is not logged and can be run again.
     *
     * @return void
     * @covers Migrations::getMigrator
     */
    public function testFailingMigration(): void
    {
        $migrator = $this->createMigrator(['failing']);

        try {
            $migrator->run($this->paths(['failing']));
            $this->fail('The failing migration should throw an exception.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Migration failed.', $exception->getMessage());
        }

        $this->assertSame(['2026_02_01_000000_create_mig_customers_table'], $this->repository($migrator)->getRan());
        $this->assertTrue($this->schema->hasTable('mig_customers'));
        $this->assertTrue($this->schema->hasTable('mig_partial'));
    }

    /**
     * @return void
     * @covers MigrationRepository::getMigrations
     * @covers MigrationRepository::getLast
     * @covers MigrationRepository::castRecords
     */
    public function testRepositoryReturnsIntegers(): void
    {
        $migrator = $this->createMigrator(['basic']);
        $migrator->run($this->paths(['basic']));

        $repository = $this->repository($migrator);
        $this->assertSame(2, $repository->getNextBatchNumber());

        foreach ([...$repository->getLast(), ...$repository->getMigrations(5)] as $record) {
            // Checked at runtime: Illuminate documents these values as int, wpdb returns strings.
            $this->assertSame('int', get_debug_type($record->id));
            $this->assertSame(1, $record->batch);
        }
    }

    /**
     * @param array<string> $directories Fixture directories
     * @param string $table
     * @return Migrator
     */
    private function createMigrator(array $directories, string $table = 'test_migrations'): Migrator
    {
        Migrations::enable([
            'paths' => $this->paths($directories),
            'table' => $table,
        ]);

        $migrator = Migrations::getMigrator();
        $this->repository($migrator)->createRepository();

        return $migrator;
    }

    /**
     * @param Migrator $migrator
     * @return MigrationRepository
     */
    private function repository(Migrator $migrator): MigrationRepository
    {
        $repository = $migrator->getRepository();
        if (!$repository instanceof MigrationRepository) {
            $this->fail(sprintf('The repository must be an instance of %s.', MigrationRepository::class));
        }

        return $repository;
    }

    /**
     * @param array<string> $directories
     * @return array<string>
     */
    private function paths(array $directories): array
    {
        return array_map(static fn (string $directory): string => self::FIXTURES . '/' . $directory, $directories);
    }
}
