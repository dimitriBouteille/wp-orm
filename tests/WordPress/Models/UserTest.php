<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Models;

use Dbout\WpOrm\Models\User;
use Illuminate\Database\Eloquent\Collection;

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
            'user_email' => self::USER_EMAIL,
        ]);
    }

    /**
     * @return void
     * @covers ::findOneByEmail
     */
    public function testFindOneByEmailWithExistingUser(): void
    {
        $this->checkFindOneResult(
            User::findOneByEmail(self::USER_EMAIL),
            'user_email',
            self::USER_EMAIL
        );
    }

    /**
     * @return void
     * @covers ::findOneByLogin
     */
    public function testFindOneByLoginWithExistingUser(): void
    {
        $this->checkFindOneResult(
            User::findOneByLogin(self::USER_LOGIN),
            'user_login',
            self::USER_LOGIN
        );
    }

    /**
     * @return void
     * @covers ::comments
     */
    public function testComments(): void
    {
        $selfComments = [
            self::factory()->comment->create([
                'user_id' => self::$testingUserId,
            ]),
            self::factory()->comment->create([
                'user_id' => self::$testingUserId,
            ]),
        ];

        /**
         * Created a comment that is not associated with this user
         */
        self::factory()->comment->create([
            'user_id' => 15050,
        ]);

        $values = $this->getTestingUser()?->comments;
        $ids = $values->pluck('comment_ID');

        $this->assertCount(2, $values->toArray());
        $this->assertEqualsCanonicalizing($selfComments, $ids->toArray());
    }

    /**
     * @return void
     * @covers ::posts
     */
    public function testPosts(): void
    {
        $selfPosts = [
            self::factory()->post->create([
                'user_id' => self::$testingUserId,
            ]),
            self::factory()->post->create([
                'user_id' => self::$testingUserId,
            ]),
        ];

        self::factory()->post->create([
            'user_id' => 15050,
        ]);

        /** @var Collection $values */
        $values = $this->getTestingUser()?->posts;
        $ids = $values->pluck('ID');

        $this->assertCount(2, $values->toArray());
        $this->assertEqualsCanonicalizing($selfPosts, $ids->toArray());
    }

    /**
     * @param User|null $user
     * @param string $whereColumn
     * @param string $whereValue
     * @return void
     */
    private function checkFindOneResult(?User $user, string $whereColumn, string $whereValue): void
    {
        //$this->checkFindOneByModel($user, User::class);
        //$this->checkFindOnyByQuery('users', $whereColumn, $whereValue);

        $this->assertEquals(self::$testingUserId, $user->getId());
        $this->assertEquals(self::USER_LOGIN, $user->getUserLogin());
        $this->assertEquals(self::USER_EMAIL, $user->getUserEmail());
    }

    /**
     * @return User|null
     */
    private function getTestingUser(): ?User
    {
        return User::find(self::$testingUserId);
    }
}
