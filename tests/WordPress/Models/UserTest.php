<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Models;

use Dbout\WpOrm\Models\User;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class UserTest extends TestCase
{
    private const USER_EMAIL = 'wp-testing@wp-orm.fr';
    private const USER_LOGIN = 'testing.wp-orm';
    private static ?int $testingUserId = null;

    /**
     * User created just to simulate a database with multiple users.
     *
     * @var int|null
     */
    private static ?int $fakeUserId = null;

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

        self::$fakeUserId = self::factory()->user->create();
    }

    /**
     * @return void
     * @covers User::findOneByEmail
     */
    public function testFindOneByEmail(): void
    {
        $this->checkFindOneResult(
            User::findOneByEmail(self::USER_EMAIL),
            'user_email',
            self::USER_EMAIL
        );
    }

    /**
     * @return void
     * @covers User::findOneByLogin
     */
    public function testFindOneByLogin(): void
    {
        $this->checkFindOneResult(
            User::findOneByLogin(self::USER_LOGIN),
            'user_login',
            self::USER_LOGIN
        );
    }

    /**
     * @return void
     * @covers User::comments
     */
    public function testComments(): void
    {
        /**
         * Create fake comment with any relation with user
         */
        self::factory()->comment->create([
            'user_id' => self::$fakeUserId,
        ]);

        $ids = self::factory()->comment->create_many(2, [
            'user_id' => self::$testingUserId,
        ]);

        $this->assertHasManyRelation(
            expectedItems: $this->getTestingUser()?->comments,
            relationProperty:  'comment_ID',
            expectedIds: $ids
        );
    }

    /**
     * @return void
     * @covers User::posts
     */
    public function testPosts(): void
    {
        /**
         * Create fake post with any relation with user
         */
        self::factory()->post->create([
            'user_id' => self::$fakeUserId,
        ]);

        $ids = self::factory()->post->create_many(3, [
            'post_author' => self::$testingUserId,
        ]);

        $this->assertHasManyRelation(
            expectedItems: $this->getTestingUser()?->posts,
            relationProperty: 'ID',
            expectedIds: $ids
        );
    }

    /**
     * @param User|null $user
     * @param string $whereColumn
     * @param string $whereValue
     * @return void
     */
    private function checkFindOneResult(?User $user, string $whereColumn, string $whereValue): void
    {
        $this->assertInstanceOf(User::class, $user);
        $this->assertFindLastQuery('users', $whereColumn, $whereValue);

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
