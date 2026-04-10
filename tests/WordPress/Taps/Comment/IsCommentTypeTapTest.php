<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Taps\Comment;

use Dbout\WpOrm\Models\Comment;
use Dbout\WpOrm\Taps\Comment\IsCommentTypeTap;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class IsCommentTypeTapTest extends TestCase
{
    /**
     * @return void
     * @covers IsCommentTypeTap::__construct
     * @covers IsCommentTypeTap::__invoke
     */
    public function testFiltersByPingbackType(): void
    {
        self::factory()->comment->create([
            'comment_type' => '',
            'comment_content' => 'Regular comment',
        ]);

        $pingbackId = self::factory()->comment->create([
            'comment_type' => 'pingback',
            'comment_content' => 'Pingback comment',
        ]);

        $comments = Comment::query()
            ->tap(new IsCommentTypeTap('pingback'))
            ->get();

        /** @var Comment $first */
        $first = $comments->first();

        $this->assertCount(1, $comments->toArray());
        $this->assertEquals($pingbackId, $first->getId());
        $this->assertEquals('pingback', $first->getCommentType());
    }

    /**
     * @return void
     * @covers IsCommentTypeTap::__invoke
     */
    public function testFiltersByCustomCommentType(): void
    {
        self::factory()->comment->create([
            'comment_type' => '',
            'comment_content' => 'Regular comment',
        ]);

        $reviewId = self::factory()->comment->create([
            'comment_type' => 'review',
            'comment_content' => 'Product review',
        ]);

        self::factory()->comment->create([
            'comment_type' => 'order_note',
            'comment_content' => 'Order note',
        ]);

        $comments = Comment::query()
            ->tap(new IsCommentTypeTap('review'))
            ->get();

        /** @var Comment $first */
        $first = $comments->first();

        $this->assertCount(1, $comments->toArray());
        $this->assertEquals($reviewId, $first->getId());
        $this->assertEquals('review', $first->getCommentType());
    }

    /**
     * @return void
     * @covers IsCommentTypeTap::__invoke
     */
    public function testReturnsMultipleCommentsWithSameType(): void
    {
        $reviewIds = [];
        $reviewIds[] = self::factory()->comment->create([
            'comment_type' => 'review',
            'comment_content' => 'First review',
        ]);
        $reviewIds[] = self::factory()->comment->create([
            'comment_type' => 'review',
            'comment_content' => 'Second review',
        ]);
        $reviewIds[] = self::factory()->comment->create([
            'comment_type' => 'review',
            'comment_content' => 'Third review',
        ]);

        self::factory()->comment->create([
            'comment_type' => '',
            'comment_content' => 'Regular comment',
        ]);
        self::factory()->comment->create([
            'comment_type' => 'pingback',
            'comment_content' => 'Pingback',
        ]);

        $comments = Comment::query()
            ->tap(new IsCommentTypeTap('review'))
            ->get();

        $this->assertCount(3, $comments->toArray());
        $this->assertEquals($reviewIds, $comments->pluck('comment_ID')->toArray());
    }

    /**
     * @return void
     * @covers IsCommentTypeTap::__invoke
     */
    public function testReturnsEmptyCollectionWhenNoCommentsMatch(): void
    {
        self::factory()->comment->create([
            'comment_type' => '',
            'comment_content' => 'Regular comment',
        ]);

        self::factory()->comment->create([
            'comment_type' => 'pingback',
            'comment_content' => 'Pingback',
        ]);

        $comments = Comment::query()
            ->tap(new IsCommentTypeTap('custom_type'))
            ->get();

        $this->assertCount(0, $comments->toArray());
    }

    /**
     * @return void
     * @covers IsCommentTypeTap::__invoke
     */
    public function testCanBeChainedWithOtherQueryMethods(): void
    {
        self::factory()->comment->create([
            'comment_type' => 'review',
            'comment_approved' => '1',
            'comment_content' => 'Approved review',
        ]);

        $unapprovedReviewId = self::factory()->comment->create([
            'comment_type' => 'review',
            'comment_approved' => '0',
            'comment_content' => 'Unapproved review',
        ]);

        self::factory()->comment->create([
            'comment_type' => 'order_note',
            'comment_approved' => '0',
            'comment_content' => 'Unapproved order note',
        ]);

        $comments = Comment::query()
            ->tap(new IsCommentTypeTap('review'))
            ->where('comment_approved', '0')
            ->get();

        /** @var Comment $first */
        $first = $comments->first();

        $this->assertCount(1, $comments->toArray());
        $this->assertEquals($unapprovedReviewId, $first->getId());
        $this->assertEquals('review', $first->getCommentType());
        $this->assertEquals('0', $first->getCommentApproved());
    }

    /**
     * @return void
     * @covers IsCommentTypeTap::__invoke
     */
    public function testCanBeChainedWithPostIdFilter(): void
    {
        $postId = self::factory()->post->create();
        $otherPostId = self::factory()->post->create();

        $expectedId = self::factory()->comment->create([
            'comment_type' => 'review',
            'comment_post_ID' => $postId,
            'comment_content' => 'Review for specific post',
        ]);

        self::factory()->comment->create([
            'comment_type' => 'review',
            'comment_post_ID' => $otherPostId,
            'comment_content' => 'Review for other post',
        ]);

        self::factory()->comment->create([
            'comment_type' => '',
            'comment_post_ID' => $postId,
            'comment_content' => 'Regular comment for post',
        ]);

        $comments = Comment::query()
            ->tap(new IsCommentTypeTap('review'))
            ->where('comment_post_ID', $postId)
            ->get();

        /** @var Comment $first */
        $first = $comments->first();

        $this->assertCount(1, $comments->toArray());
        $this->assertEquals($expectedId, $first->getId());
        $this->assertEquals('review', $first->getCommentType());
        $this->assertEquals($postId, $first->getCommentPostID());
    }

    /**
     * @return void
     * @covers IsCommentTypeTap::__invoke
     */
    public function testGeneratesCorrectSqlQuery(): void
    {
        Comment::query()
            ->tap(new IsCommentTypeTap('review'))
            ->get();

        $this->assertLastQueryEquals(
            "select `#TABLE_PREFIX#comments`.* from `#TABLE_PREFIX#comments` where `comment_type` = 'review'"
        );
    }

    /**
     * @return void
     * @covers IsCommentTypeTap::__invoke
     */
    public function testGeneratesCorrectSqlQueryForEmptyType(): void
    {
        Comment::query()
            ->tap(new IsCommentTypeTap(''))
            ->get();

        $this->assertLastQueryEquals(
            "select `#TABLE_PREFIX#comments`.* from `#TABLE_PREFIX#comments` where `comment_type` = ''"
        );
    }

    /**
     * @return void
     * @covers IsCommentTypeTap::__invoke
     */
    public function testDistinguishesBetweenDifferentCustomTypes(): void
    {
        self::factory()->comment->create([
            'comment_type' => 'review',
            'comment_content' => 'Product review',
        ]);

        $actionLogId = self::factory()->comment->create([
            'comment_type' => 'action_log',
            'comment_content' => 'Action logged',
        ]);

        self::factory()->comment->create([
            'comment_type' => 'notification',
            'comment_content' => 'System notification',
        ]);

        $comments = Comment::query()
            ->tap(new IsCommentTypeTap('action_log'))
            ->get();

        /** @var Comment $first */
        $first = $comments->first();

        $this->assertCount(1, $comments->toArray());
        $this->assertEquals($actionLogId, $first->getId());
        $this->assertEquals('action_log', $first->getCommentType());
    }
}
