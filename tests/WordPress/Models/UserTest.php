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
        self::$testingUserId = self::factory()->user->create([
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
        $this->checkFindOneResult($user, 'user_email', self::USER_EMAIL);
    }

    /**
     * @return void
     * @covers ::findOneByLogin
     */
    public function testFindOneByLoginWithExistingUser(): void
    {
        $user = User::findOneByLogin(self::USER_LOGIN);
        $this->checkFindOneResult($user, 'user_login', self::USER_LOGIN);
    }

    /**
     * @param User|null $user
     * @param string $whereColumn
     * @param string $whereValue
     * @return void
     */
    private function checkFindOneResult(?User $user, string $whereColumn, string $whereValue): void
    {
        global $wpdb;

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(self::$testingUserId, $user->getId());
        $this->assertEquals(self::USER_LOGIN, $user->getUserLogin());
        $this->assertEquals(self::USER_EMAIL, $user->getUserEmail());

        $this->assertEquals(
            sprintf(
                "select `wptests_users`.* from `wptests_users` where `%s` = '%s' limit 1",
                $whereColumn,
                $whereValue
            ),
            $wpdb->last_query
        );
    }
}