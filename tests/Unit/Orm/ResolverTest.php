<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\Unit\Orm;

use Dbout\WpOrm\Orm\Database;
use Dbout\WpOrm\Orm\Resolver;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[CoversClass(Resolver::class)]
class ResolverTest extends TestCase
{
    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Provide a mocked $wpdb so Database::getInstance() can build a singleton.
        $wpdb = $this->createStub(\wpdb::class);
        $wpdb->prefix = 'wp_';
        $wpdb->charset = 'utf8mb4';
        $wpdb->collate = 'utf8mb4_unicode_ci';
        $wpdb->method('db_version')->willReturn('8.0.0');

        global $wpdb_global_backup;
        $wpdb_global_backup = $GLOBALS['wpdb'] ?? null;
        $GLOBALS['wpdb'] = $wpdb;

        if (!defined('DB_NAME')) {
            define('DB_NAME', 'test_db');
        }

        // Reset Database singleton between tests.
        $reflection = new \ReflectionClass(Database::class);
        $instance = $reflection->getProperty('instance');
        $instance->setValue(null, null);
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        global $wpdb_global_backup;
        $GLOBALS['wpdb'] = $wpdb_global_backup;

        parent::tearDown();
    }

    /**
     * Pin: Resolver::connection() ignores its $name argument.
     *
     * Resolver always returns the Database singleton regardless of the
     * requested connection name, so it is impossible to register multiple
     * connections. If multi-connection support is ever added, this test will
     * fail and signal the contract change.
     *
     * @return void
     */
    #[Group('regression-pin')]
    public function testConnectionReturnsSameInstanceRegardlessOfName(): void
    {
        $resolver = new Resolver();

        $default = $resolver->connection();
        $named   = $resolver->connection('something_else');
        $other   = $resolver->connection('yet_another');

        $this->assertSame($default, $named);
        $this->assertSame($default, $other);
    }

    /**
     * Pin: Resolver::getDefaultConnection() returns '' when never set.
     *
     * @return void
     */
    #[Group('regression-pin')]
    public function testGetDefaultConnectionReturnsEmptyStringWhenUnset(): void
    {
        $resolver = new Resolver();
        $this->assertSame('', $resolver->getDefaultConnection());
    }

    /**
     * @return void
     */
    public function testSetDefaultConnectionStoresName(): void
    {
        $resolver = new Resolver();
        $resolver->setDefaultConnection('main');

        $this->assertSame('main', $resolver->getDefaultConnection());
    }
}
