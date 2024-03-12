<?php

namespace Dbout\WpOrm\Tests\WordPress\Models;

use Dbout\WpOrm\Models\User;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Dbout\WpOrm\Models\User
 */
class UserTest extends TestCase
{
    private const USER_EMAIL = 'wp-testing@wp-orm.fr';
    private const USER_LOGIN = 'testing.wp-orm';
    private static ?int $testingUserId = null;

    /**
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        self::$testingUserId = wp_insert_user([
            'user_login' => self::USER_LOGIN,
            'user_pass'  => 'testing',
            'user_email' => self::USER_EMAIL
        ]);
    }

    /**
     * @return void
     * @covers ::findOneByEmail
     */
    public function testFindOneByEmailWithExistingUser(): void
    {
        $user = User::findOneByEmail(self::USER_EMAIL);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(self::$testingUserId, $user->getId());
        $this->assertEquals(self::USER_LOGIN, $user->getUserLogin());
    }

    /**
     * @return void
     * @covers ::findOneByEmail
     */
    public function testFindOneByEmailWithInvalidUser(): void
    {
        $user = User::findOneByEmail('fake-user@wp-orm.fr');
        $this->assertNull($user);
    }
}