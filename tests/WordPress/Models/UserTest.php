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
    private ?int $testingUserId = null;

    /**
     * @return void
     */
    public function setUpBeforeClass(): void
    {
        $this->testingUserId = wp_insert_user([
            'user_login' => self::USER_LOGIN,
            'user_pass'  => 'testing',
            'user_email' => self::USER_EMAIL
        ]);
    }

    /**
     * @return void
     * @covers ::findOneByEmail
     */
    public function testFindOneByEmail(): void
    {
        $user = User::findOneByEmail(self::USER_EMAIL);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($this->testingUserId, $user->getId());
    }
}