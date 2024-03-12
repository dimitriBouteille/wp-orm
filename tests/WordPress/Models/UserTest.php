<?php

namespace Dbout\WpOrm\Tests\WordPress\Models;

use Dbout\WpOrm\Models\User;

/**
 * @coversDefaultClass \Dbout\WpOrm\Models\User
 */
class UserTest extends \WP_UnitTestCase
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
        global $wpdb;
        $user = User::findOneByEmail(self::USER_EMAIL);
        $this->returnUserTests($user);
        var_dump($wpdb->last_query);
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

    /**
     * @return void
     * @covers ::findOneByLogin
     */
    public function testFindOneByLoginWithExistingUser(): void
    {
        $user = User::findOneByLogin(self::USER_LOGIN);
        $this->returnUserTests($user);
    }

    /**
     * @param User|null $user
     * @return void
     */
    private function returnUserTests(?User $user): void
    {
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(self::$testingUserId, $user->getId());
        $this->assertEquals(self::USER_LOGIN, $user->getUserLogin());
    }
}