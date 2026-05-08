<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Taps\Comment;

use Dbout\WpOrm\Models\Comment;
use Dbout\WpOrm\Models\User;
use Dbout\WpOrm\Taps\Comment\IsUserTap;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class IsUserTapTest extends TestCase
{
    /**
     * @return void
     * @covers IsUserTap::__construct
     * @covers IsUserTap::__invoke
     */
    public function testFiltersByUserIdAsInteger(): void
    {
        $userId = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => 'john@example.com',
        ]);

        $otherUserId = self::factory()->user->create([
            'user_login' => 'jane_doe',
            'user_email' => 'jane@example.com',
        ]);

        $expectedId = self::factory()->comment->create([
            'user_id' => $userId,
            'comment_content' => 'Comment by John',
        ]);

        self::factory()->comment->create([
            'user_id' => $otherUserId,
            'comment_content' => 'Comment by Jane',
        ]);

        self::factory()->comment->create([
            'user_id' => 0,
            'comment_content' => 'Guest comment',
        ]);

        $comments = Comment::query()
            ->tap(new IsUserTap($userId))
            ->get();

        /** @var Comment $first */
        $first = $comments->first();

        $this->assertCount(1, $comments->toArray());
        $this->assertEquals($expectedId, $first->getId());
        $this->assertEquals($userId, $first->getUserId());
    }

    /**
     * @return void
     * @covers IsUserTap::__invoke
     */
    public function testFiltersByUserModel(): void
    {
        $userId = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => 'john@example.com',
        ]);

        $otherUserId = self::factory()->user->create([
            'user_login' => 'jane_doe',
            'user_email' => 'jane@example.com',
        ]);

        $expectedId = self::factory()->comment->create([
            'user_id' => $userId,
            'comment_content' => 'Comment by John',
        ]);

        self::factory()->comment->create([
            'user_id' => $otherUserId,
            'comment_content' => 'Comment by Jane',
        ]);

        $user = User::find($userId);

        $comments = Comment::query()
            ->tap(new IsUserTap($user))
            ->get();

        /** @var Comment $first */
        $first = $comments->first();

        $this->assertCount(1, $comments->toArray());
        $this->assertEquals($expectedId, $first->getId());
        $this->assertEquals($userId, $first->getUserId());
    }

    /**
     * @return void
     * @covers IsUserTap::__invoke
     */
    public function testReturnsMultipleCommentsFromSameUser(): void
    {
        $userId = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => 'john@example.com',
        ]);

        $otherUserId = self::factory()->user->create([
            'user_login' => 'jane_doe',
            'user_email' => 'jane@example.com',
        ]);

        $expectedIds = [];
        $expectedIds[] = self::factory()->comment->create([
            'user_id' => $userId,
            'comment_content' => 'First comment by John',
        ]);
        $expectedIds[] = self::factory()->comment->create([
            'user_id' => $userId,
            'comment_content' => 'Second comment by John',
        ]);
        $expectedIds[] = self::factory()->comment->create([
            'user_id' => $userId,
            'comment_content' => 'Third comment by John',
        ]);

        self::factory()->comment->create([
            'user_id' => $otherUserId,
            'comment_content' => 'Comment by Jane',
        ]);

        $comments = Comment::query()
            ->tap(new IsUserTap($userId))
            ->get();

        $this->assertCount(3, $comments->toArray());
        $this->assertEquals($expectedIds, $comments->pluck('comment_ID')->toArray());
    }

    /**
     * @return void
     * @covers IsUserTap::__invoke
     */
    public function testFiltersGuestCommentsByZeroUserId(): void
    {
        $userId = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => 'john@example.com',
        ]);

        self::factory()->comment->create([
            'user_id' => $userId,
            'comment_content' => 'Logged in user comment',
        ]);

        $guestId = self::factory()->comment->create([
            'user_id' => 0,
            'comment_content' => 'Guest comment',
        ]);

        $comments = Comment::query()
            ->tap(new IsUserTap(0))
            ->get();

        /** @var Comment $first */
        $first = $comments->first();

        $this->assertCount(1, $comments->toArray());
        $this->assertEquals($guestId, $first->getId());
        $this->assertEquals(0, $first->getUserId());
    }

    /**
     * @return void
     * @covers IsUserTap::__invoke
     */
    public function testReturnsEmptyCollectionWhenUserHasNoComments(): void
    {
        $userId = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => 'john@example.com',
        ]);

        $otherUserId = self::factory()->user->create([
            'user_login' => 'jane_doe',
            'user_email' => 'jane@example.com',
        ]);

        self::factory()->comment->create([
            'user_id' => $otherUserId,
            'comment_content' => 'Comment by Jane',
        ]);

        $comments = Comment::query()
            ->tap(new IsUserTap($userId))
            ->get();

        $this->assertCount(0, $comments->toArray());
    }

    /**
     * @return void
     * @covers IsUserTap::__invoke
     */
    public function testCanBeChainedWithCommentTypeFilter(): void
    {
        $userId = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => 'john@example.com',
        ]);

        self::factory()->comment->create([
            'user_id' => $userId,
            'comment_type' => '',
            'comment_content' => 'Regular comment by John',
        ]);

        $expectedId = self::factory()->comment->create([
            'user_id' => $userId,
            'comment_type' => 'review',
            'comment_content' => 'Review by John',
        ]);

        self::factory()->comment->create([
            'user_id' => 0,
            'comment_type' => 'review',
            'comment_content' => 'Review by guest',
        ]);

        $comments = Comment::query()
            ->tap(new IsUserTap($userId))
            ->where('comment_type', 'review')
            ->get();

        /** @var Comment $first */
        $first = $comments->first();

        $this->assertCount(1, $comments->toArray());
        $this->assertEquals($expectedId, $first->getId());
        $this->assertEquals($userId, $first->getUserId());
        $this->assertEquals('review', $first->getCommentType());
    }

    /**
     * @return void
     * @covers IsUserTap::__invoke
     */
    public function testCanBeChainedWithPostIdFilter(): void
    {
        $userId = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => 'john@example.com',
        ]);

        $postId = self::factory()->post->create();
        $otherPostId = self::factory()->post->create();

        $expectedId = self::factory()->comment->create([
            'user_id' => $userId,
            'comment_post_ID' => $postId,
            'comment_content' => 'Comment on specific post',
        ]);

        self::factory()->comment->create([
            'user_id' => $userId,
            'comment_post_ID' => $otherPostId,
            'comment_content' => 'Comment on other post',
        ]);

        $comments = Comment::query()
            ->tap(new IsUserTap($userId))
            ->where('comment_post_ID', $postId)
            ->get();

        /** @var Comment $first */
        $first = $comments->first();

        $this->assertCount(1, $comments->toArray());
        $this->assertEquals($expectedId, $first->getId());
        $this->assertEquals($userId, $first->getUserId());
        $this->assertEquals($postId, $first->getCommentPostID());
    }

    /**
     * @return void
     * @covers IsUserTap::__invoke
     */
    public function testDistinguishesBetweenDifferentUsers(): void
    {
        $user1Id = self::factory()->user->create([
            'user_login' => 'user1',
            'user_email' => 'user1@example.com',
        ]);

        $user2Id = self::factory()->user->create([
            'user_login' => 'user2',
            'user_email' => 'user2@example.com',
        ]);

        $user3Id = self::factory()->user->create([
            'user_login' => 'user3',
            'user_email' => 'user3@example.com',
        ]);

        $expectedId = self::factory()->comment->create([
            'user_id' => $user2Id,
            'comment_content' => 'Comment by user 2',
        ]);

        self::factory()->comment->create([
            'user_id' => $user1Id,
            'comment_content' => 'Comment by user 1',
        ]);

        self::factory()->comment->create([
            'user_id' => $user3Id,
            'comment_content' => 'Comment by user 3',
        ]);

        $comments = Comment::query()
            ->tap(new IsUserTap($user2Id))
            ->get();

        /** @var Comment $first */
        $first = $comments->first();

        $this->assertCount(1, $comments->toArray());
        $this->assertEquals($expectedId, $first->getId());
        $this->assertEquals($user2Id, $first->getUserId());
    }
}
