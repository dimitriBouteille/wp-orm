<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Migrations\Commands;

use Dbout\WpOrm\Migrations\Commands\AbstractCommand;
use Dbout\WpOrm\Migrations\Migrations;
use Dbout\WpOrm\Orm\Database;
use Dbout\WpOrm\Orm\Schemas\WordPressBuilder;
use Dbout\WpOrm\Tests\WordPress\TestCase;
use WP_CLI\ExitException;
use WP_CLI\Loggers\Execution;

/**
 * Runs the WP-CLI command classes directly, the output is captured with the WP-CLI execution logger
 * and WP_CLI::error() / WP_CLI::halt() throw an ExitException instead of exiting.
 *
 * Migrations execute real DDL statements: every table created here is dropped in tearDown().
 */
abstract class CommandTestCase extends TestCase
{
    protected const string FIXTURES = __DIR__ . '/../Fixtures';
    protected const string TABLE = 'test_migrations';
    private const array TABLES = ['mig_orders', 'mig_customers', 'mig_partial', self::TABLE];

    protected WordPressBuilder $schema;
    protected Execution $logger;

    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Loaded by the WP-CLI runner in real life.
        require_once dirname(__DIR__, 4) . '/vendor/wp-cli/wp-cli/php/utils.php';
    }

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->schema = Database::getInstance()->getSchemaBuilder();
        Migrations::reset();

        $this->logger = new Execution();
        \WP_CLI::set_logger($this->logger);
        self::setCaptureExit(true);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        self::setCaptureExit(false);
        Migrations::reset();
        foreach (self::TABLES as $table) {
            $this->schema->dropIfExists($table);
        }

        parent::tearDown();
    }

    /**
     * Make WP_CLI::error() and WP_CLI::halt() throw an ExitException instead of exiting.
     * WP-CLI only toggles this private flag internally (WP_CLI::runcommand()).
     *
     * @param bool $capture
     * @return void
     */
    private static function setCaptureExit(bool $capture): void
    {
        (new \ReflectionProperty(\WP_CLI::class, 'capture_exit'))->setValue(null, $capture);
    }

    /**
     * @param array<string> $directories Fixture directories
     * @return void
     */
    protected function enable(array $directories = ['basic']): void
    {
        Migrations::enable([
            'paths' => array_map(
                static fn (string $directory): string => self::FIXTURES . '/' . $directory,
                $directories
            ),
            'table' => self::TABLE,
        ]);
    }

    /**
     * @param AbstractCommand $command
     * @param array<string, mixed> $assocArgs
     * @return int Exit code
     */
    protected function runCommand(AbstractCommand $command, array $assocArgs = []): int
    {
        try {
            $command([], $assocArgs);
        } catch (ExitException $exception) {
            return $exception->getCode();
        }

        return 0;
    }

    /**
     * @return array<string>
     */
    protected function getRan(): array
    {
        return Migrations::getMigrator()->getRepository()->getRan();
    }

    /**
     * @param string $expected
     * @return void
     */
    protected function assertOutputContains(string $expected): void
    {
        self::assertStringContainsString($expected, $this->logger->stdout);
    }

    /**
     * @param string $expected
     * @return void
     */
    protected function assertErrorOutputContains(string $expected): void
    {
        self::assertStringContainsString($expected, $this->logger->stderr);
    }
}
