<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Builders;

use Dbout\WpOrm\Builders\UserBuilder;
use Dbout\WpOrm\Models\User;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class UserBuilderTest extends TestCase
{
    /**
     * @return void
     * @covers UserBuilder::whereEmail
     */
    public function testWhereEmailFiltersByEmail(): void
    {
        $userId = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => 'john@example.com',
        ]);

        self::factory()->user->create([
            'user_login' => 'jane_doe',
            'user_email' => 'jane@example.com',
        ]);

        $users = User::query()
            ->whereEmail('john@example.com')
            ->get();

        /** @var User $first */
        $first = $users->first();

        $this->assertCount(1, $users->toArray());
        $this->assertEquals($userId, $first->getId());
        $this->assertEquals('john@example.com', $first->getUserEmail());
    }

    /**
     * @return void
     * @covers UserBuilder::whereLogin
     */
    public function testWhereLoginFiltersByLogin(): void
    {
        $userId = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => 'john@example.com',
        ]);

        self::factory()->user->create([
            'user_login' => 'jane_doe',
            'user_email' => 'jane@example.com',
        ]);

        $users = User::query()
            ->whereLogin('john_doe')
            ->get();

        /** @var User $first */
        $first = $users->first();

        $this->assertCount(1, $users->toArray());
        $this->assertEquals($userId, $first->getId());
        $this->assertEquals('john_doe', $first->getUserLogin());
    }

    /**
     * @return void
     * @covers UserBuilder::whereEmails
     */
    public function testWhereEmailsFiltersByMultipleEmails(): void
    {
        $userId1 = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => 'john@example.com',
        ]);

        $userId2 = self::factory()->user->create([
            'user_login' => 'jane_doe',
            'user_email' => 'jane@example.com',
        ]);

        self::factory()->user->create([
            'user_login' => 'bob_smith',
            'user_email' => 'bob@example.com',
        ]);

        $users = User::query()
            ->whereEmails('john@example.com', 'jane@example.com')
            ->get();

        $this->assertCount(2, $users->toArray());
        $ids = $users->pluck('ID')->toArray();
        $this->assertEqualsCanonicalizing([$userId1, $userId2], $ids);
    }

    /**
     * @return void
     * @covers UserBuilder::whereLogins
     */
    public function testWhereLoginsFiltersByMultipleLogins(): void
    {
        $userId1 = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => 'john@example.com',
        ]);

        $userId2 = self::factory()->user->create([
            'user_login' => 'jane_doe',
            'user_email' => 'jane@example.com',
        ]);

        self::factory()->user->create([
            'user_login' => 'bob_smith',
            'user_email' => 'bob@example.com',
        ]);

        $users = User::query()
            ->whereLogins('john_doe', 'jane_doe')
            ->get();

        $this->assertCount(2, $users->toArray());
        $ids = $users->pluck('ID')->toArray();
        $this->assertEqualsCanonicalizing([$userId1, $userId2], $ids);
    }

    /**
     * @return void
     * @covers UserBuilder::whereEmail
     */
    public function testWhereEmailReturnsEmptyWhenNoMatch(): void
    {
        self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => 'john@example.com',
        ]);

        $users = User::query()
            ->whereEmail('nonexistent@example.com')
            ->get();

        $this->assertCount(0, $users->toArray());
    }

    /**
     * @return void
     * @covers UserBuilder::whereLogin
     */
    public function testWhereLoginReturnsEmptyWhenNoMatch(): void
    {
        self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => 'john@example.com',
        ]);

        $users = User::query()
            ->whereLogin('nonexistent_user')
            ->get();

        $this->assertCount(0, $users->toArray());
    }

    /**
     * @return void
     * @covers UserBuilder::whereLogin
     */
    public function testWhereLoginCanBeChainedWithWhere(): void
    {
        $userId = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => 'john@example.com',
            'display_name' => 'John Doe',
        ]);

        self::factory()->user->create([
            'user_login' => 'jane_doe',
            'user_email' => 'jane@example.com',
            'display_name' => 'Jane Doe',
        ]);

        $users = User::query()
            ->whereLogin('john_doe')
            ->where('display_name', 'John Doe')
            ->get();

        /** @var User $first */
        $first = $users->first();

        $this->assertCount(1, $users->toArray());
        $this->assertEquals($userId, $first->getId());
    }

    /**
     * @return void
     * @covers UserBuilder::whereEmail
     */
    public function testWhereEmailGeneratesCorrectSql(): void
    {
        User::query()
            ->whereEmail('john@example.com')
            ->get();

        $this->assertLastQueryEquals(
            "select `#TABLE_PREFIX#users`.* from `#TABLE_PREFIX#users` where `user_email` = 'john@example.com'"
        );
    }

    /**
     * @return void
     * @covers UserBuilder::whereLogin
     */
    public function testWhereLoginGeneratesCorrectSql(): void
    {
        User::query()
            ->whereLogin('john_doe')
            ->get();

        $this->assertLastQueryEquals(
            "select `#TABLE_PREFIX#users`.* from `#TABLE_PREFIX#users` where `user_login` = 'john_doe'"
        );
    }

    /**
     * @return void
     * @covers UserBuilder::whereEmails
     */
    public function testWhereEmailsWithSingleEmail(): void
    {
        $userId = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => 'john@example.com',
        ]);

        self::factory()->user->create([
            'user_login' => 'jane_doe',
            'user_email' => 'jane@example.com',
        ]);

        $users = User::query()
            ->whereEmails('john@example.com')
            ->get();

        /** @var User $first */
        $first = $users->first();

        $this->assertCount(1, $users->toArray());
        $this->assertEquals($userId, $first->getId());
    }

    /**
     * @return void
     * @covers UserBuilder::whereLogins
     */
    public function testWhereLoginsWithSingleLogin(): void
    {
        $userId = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => 'john@example.com',
        ]);

        self::factory()->user->create([
            'user_login' => 'jane_doe',
            'user_email' => 'jane@example.com',
        ]);

        $users = User::query()
            ->whereLogins('john_doe')
            ->get();

        /** @var User $first */
        $first = $users->first();

        $this->assertCount(1, $users->toArray());
        $this->assertEquals($userId, $first->getId());
    }

    /**
     * @return void
     * @covers UserBuilder::whereEmails
     */
    public function testWhereEmailsWithThreeEmails(): void
    {
        $userId1 = self::factory()->user->create([
            'user_login' => 'user1',
            'user_email' => 'user1@example.com',
        ]);

        $userId2 = self::factory()->user->create([
            'user_login' => 'user2',
            'user_email' => 'user2@example.com',
        ]);

        $userId3 = self::factory()->user->create([
            'user_login' => 'user3',
            'user_email' => 'user3@example.com',
        ]);

        self::factory()->user->create([
            'user_login' => 'user4',
            'user_email' => 'user4@example.com',
        ]);

        $users = User::query()
            ->whereEmails('user1@example.com', 'user2@example.com', 'user3@example.com')
            ->get();

        $this->assertCount(3, $users->toArray());
        $ids = $users->pluck('ID')->toArray();
        $this->assertEqualsCanonicalizing([$userId1, $userId2, $userId3], $ids);
    }

    /**
     * @return void
     * @covers UserBuilder::whereLogins
     */
    public function testWhereLoginsWithThreeLogins(): void
    {
        $userId1 = self::factory()->user->create([
            'user_login' => 'user1',
            'user_email' => 'user1@example.com',
        ]);

        $userId2 = self::factory()->user->create([
            'user_login' => 'user2',
            'user_email' => 'user2@example.com',
        ]);

        $userId3 = self::factory()->user->create([
            'user_login' => 'user3',
            'user_email' => 'user3@example.com',
        ]);

        self::factory()->user->create([
            'user_login' => 'user4',
            'user_email' => 'user4@example.com',
        ]);

        $users = User::query()
            ->whereLogins('user1', 'user2', 'user3')
            ->get();

        $this->assertCount(3, $users->toArray());
        $ids = $users->pluck('ID')->toArray();
        $this->assertEqualsCanonicalizing([$userId1, $userId2, $userId3], $ids);
    }

    /**
     * @return void
     * @covers UserBuilder::whereEmail
     */
    public function testWhereEmailWorksWithFirst(): void
    {
        $userId = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => 'john@example.com',
        ]);

        /** @var User $user */
        $user = User::query()
            ->whereEmail('john@example.com')
            ->first();

        $this->assertNotNull($user);
        $this->assertEquals($userId, $user->getId());
        $this->assertEquals('john@example.com', $user->getUserEmail());
    }

    /**
     * @return void
     * @covers UserBuilder::whereLogin
     */
    public function testWhereLoginWorksWithFirst(): void
    {
        $userId = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => 'john@example.com',
        ]);

        /** @var User $user */
        $user = User::query()
            ->whereLogin('john_doe')
            ->first();

        $this->assertNotNull($user);
        $this->assertEquals($userId, $user->getId());
        $this->assertEquals('john_doe', $user->getUserLogin());
    }

    /**
     * @return void
     * @covers UserBuilder::whereEmails
     */
    public function testWhereEmailsWorksWithCount(): void
    {
        self::factory()->user->create([
            'user_login' => 'user1',
            'user_email' => 'user1@example.com',
        ]);

        self::factory()->user->create([
            'user_login' => 'user2',
            'user_email' => 'user2@example.com',
        ]);

        self::factory()->user->create([
            'user_login' => 'user3',
            'user_email' => 'user3@example.com',
        ]);

        $count = User::query()
            ->whereEmails('user1@example.com', 'user2@example.com')
            ->count();

        $this->assertEquals(2, $count);
    }

    /**
     * @return void
     * @covers UserBuilder::whereLogins
     */
    public function testWhereLoginsWorksWithCount(): void
    {
        self::factory()->user->create([
            'user_login' => 'user1',
            'user_email' => 'user1@example.com',
        ]);

        self::factory()->user->create([
            'user_login' => 'user2',
            'user_email' => 'user2@example.com',
        ]);

        self::factory()->user->create([
            'user_login' => 'user3',
            'user_email' => 'user3@example.com',
        ]);

        $count = User::query()
            ->whereLogins('user1', 'user2')
            ->count();

        $this->assertEquals(2, $count);
    }

    /**
     * @return void
     * @covers UserBuilder::whereEmails
     */
    public function testWhereEmailsReturnsEmptyWhenNoMatch(): void
    {
        self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => 'john@example.com',
        ]);

        $users = User::query()
            ->whereEmails('nonexistent1@example.com', 'nonexistent2@example.com')
            ->get();

        $this->assertCount(0, $users->toArray());
    }

    /**
     * @return void
     * @covers UserBuilder::whereLogins
     */
    public function testWhereLoginsReturnsEmptyWhenNoMatch(): void
    {
        self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => 'john@example.com',
        ]);

        $users = User::query()
            ->whereLogins('nonexistent1', 'nonexistent2')
            ->get();

        $this->assertCount(0, $users->toArray());
    }

    /**
     * @return void
     * @covers UserBuilder::whereEmail
     * @covers UserBuilder::whereLogin
     */
    public function testCombiningWhereEmailAndWhereLogin(): void
    {
        self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => 'john@example.com',
        ]);

        // This should return empty because both conditions must match the same user
        $users = User::query()
            ->whereEmail('john@example.com')
            ->whereLogin('jane_doe')
            ->get();

        $this->assertCount(0, $users->toArray());
    }
}
