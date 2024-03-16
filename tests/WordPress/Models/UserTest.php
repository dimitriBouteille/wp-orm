<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Models;

use Dbout\WpOrm\Models\User;
use Dbout\WpOrm\Tests\WordPress\Helpers\WithFindOneBy;
use Dbout\WpOrm\Tests\WordPress\Helpers\WithHasManyRelation;
use Dbout\WpOrm\Tests\WordPress\TestCase;

/**
 * @coversDefaultClass \Dbout\WpOrm\Models\User
 */
class UserTest extends TestCase
{
    use WithFindOneBy;
    use WithHasManyRelation;

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
        /**
         * Create fake comment with any relation with user
         */
        self::factory()->comment->create([
            'user_id' => self::$fakeUserId,
        ]);

        $this->checkHasManyRelationResult(
            resultCollectionCallback: fn () => $this->getTestingUser()?->comments,
            relationProperty:  'comment_ID',
            expectedIdsCallback: function () {
                return [
                    self::factory()->comment->create([
                        'user_id' => self::$testingUserId,
                    ]),
                    self::factory()->comment->create([
                        'user_id' => self::$testingUserId,
                    ]),
                ];
            }
        );
    }

    /**
     * @return void
     * @covers ::posts
     */
    public function testPosts(): void
    {
        /**
         * Create fake post with any relation with user
         */
        self::factory()->post->create([
            'user_id' => self::$fakeUserId,
        ]);

        $this->checkHasManyRelationResult(
            resultCollectionCallback: fn () => $this->getTestingUser()?->posts,
            relationProperty: 'ID',
            expectedIdsCallback: function () {
                return [
                    self::factory()->post->create([
                        'post_author' => self::$testingUserId,
                    ]),
                    self::factory()->post->create([
                        'post_author' => self::$testingUserId,
                    ]),
                ];
            }
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
        $this->checkFindOneByQuery('users', $whereColumn, $whereValue);

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
