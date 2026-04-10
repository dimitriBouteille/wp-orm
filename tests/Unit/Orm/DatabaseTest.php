<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\Unit\Orm;

use Dbout\WpOrm\Exceptions\WpOrmException;
use Dbout\WpOrm\Orm\Database;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

#[CoversClass(Database::class)]
#[CoversMethod(Database::class, 'getInstance')]
#[CoversMethod(Database::class, 'isMaria')]
#[CoversMethod(Database::class, 'getServerVersion')]
class DatabaseTest extends TestCase
{
    /**
     * @return void
     */
    public function testInvalidWPInstance(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The global variable $wpdb must be instance of \wpdb.');
        Database::getInstance();
    }

    /**
     * @param string $serverInfo
     * @return void
     */
    #[TestWith(['10.5.8-MariaDB-1:10.5.8+maria~focal'])]
    #[TestWith(['10.5.8-MARIADB-1:10.5.8+maria~focal'])]
    public function testIsMariaReturnsTrueWhenServerInfoContainsMariadb(string $serverInfo): void
    {
        $database = $this->createDatabaseWithMockedWpdb([
            'db_server_info' => $serverInfo,
        ]);

        $this->assertTrue($database->isMaria());
    }

    /**
     * @return void
     */
    public function testIsMariaReturnsFalseWhenServerInfoDoesNotContainMariadb(): void
    {
        $database = $this->createDatabaseWithMockedWpdb([
            'db_server_info' => '8.0.27-MySQL Community Server - GPL',
        ]);

        $this->assertFalse($database->isMaria());
    }

    /**
     * @return void
     */
    public function testIsMariaCachesResult(): void
    {
        $wpdb = $this->createMock(\wpdb::class);
        $wpdb->expects($this->once())
            ->method('db_server_info')
            ->willReturn('10.5.8-MariaDB-1:10.5.8+maria~focal');

        $wpdb->prefix = 'wp_';
        $wpdb->charset = 'utf8mb4';
        $wpdb->collate = 'utf8mb4_unicode_ci';
        $wpdb->method('db_version')->willReturn('10.5.8');

        $database = $this->createDatabaseInstance($wpdb);

        $this->assertTrue($database->isMaria());

        $this->assertTrue($database->isMaria());
    }

    /**
     * @throws WpOrmException
     * @return void
     */
    public function testGetServerVersionReturnsVersion(): void
    {
        $database = $this->createDatabaseWithMockedWpdb([
            'db_version' => '8.0.27',
        ]);

        $this->assertEquals('8.0.27', $database->getServerVersion());
    }

    /**
     * @param mixed $version
     * @throws WpOrmException
     * @return void
     */
    #[TestWith([''])]
    #[TestWith([null])]
    public function testGetServerVersionThrowsExceptionWhenVersionIsUndefined(mixed $version): void
    {
        $database = $this->createDatabaseWithMockedWpdb([
            'db_version' => $version,
        ]);

        $this->expectException(WpOrmException::class);
        $this->expectExceptionMessage('Unable to retrieve the server version.');
        $database->getServerVersion();
    }

    /**
     * @param array $config Configuration for mocked methods
     * @return Database
     */
    private function createDatabaseWithMockedWpdb(array $config): Database
    {
        $wpdb = $this->createStub(\wpdb::class);

        if (isset($config['db_server_info'])) {
            $wpdb->method('db_server_info')->willReturn($config['db_server_info']);
        }

        if (array_key_exists('db_version', $config)) {
            $wpdb->method('db_version')->willReturn($config['db_version']);
        }

        $wpdb->prefix = 'wp_';
        $wpdb->charset = 'utf8mb4';
        $wpdb->collate = 'utf8mb4_unicode_ci';

        return $this->createDatabaseInstance($wpdb);
    }

    /**
     * @param \wpdb $mockedWpdb
     * @return Database
     */
    private function createDatabaseInstance(\wpdb $mockedWpdb): Database
    {
        // Set the global $wpdb variable
        global $wpdb;
        $originalWpdb = $wpdb ?? null;
        $wpdb = $mockedWpdb;

        // Define DB_NAME if not already defined
        if (!defined('DB_NAME')) {
            define('DB_NAME', 'test_db');
        }

        $database = new Database();

        // Restore original $wpdb
        $wpdb = $originalWpdb;

        return $database;
    }
}
