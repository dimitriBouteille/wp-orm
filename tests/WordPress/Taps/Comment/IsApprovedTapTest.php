<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Taps\Comment;

use Dbout\WpOrm\Models\Comment;
use Dbout\WpOrm\Taps\Comment\IsApprovedTap;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class IsApprovedTapTest extends TestCase
{
    /**
     * @return void
     * @covers IsApprovedTap::__construct
     * @covers IsApprovedTap::__invoke
     */
    public function testFiltersApprovedComments(): void
    {
        $approvedId = self::factory()->comment->create([
            'comment_approved' => '1',
            'comment_content' => 'Approved comment',
        ]);

        self::factory()->comment->create([
            'comment_approved' => '0',
            'comment_content' => 'Unapproved comment',
        ]);

        self::factory()->comment->create([
            'comment_approved' => 'spam',
            'comment_content' => 'Spam comment',
        ]);

        $comments = Comment::query()
            ->tap(new IsApprovedTap(true))
            ->get();

        /** @var Comment $first */
        $first = $comments->first();

        $this->assertCount(1, $comments->toArray());
        $this->assertEquals($approvedId, $first->getId());
        $this->assertEquals('1', $first->getCommentApproved());
    }

    /**
     * @return void
     * @covers IsApprovedTap::__invoke
     */
    public function testFiltersUnapprovedComments(): void
    {
        self::factory()->comment->create([
            'comment_approved' => '1',
            'comment_content' => 'Approved comment',
        ]);

        $unapprovedId = self::factory()->comment->create([
            'comment_approved' => '0',
            'comment_content' => 'Unapproved comment',
        ]);

        $comments = Comment::query()
            ->tap(new IsApprovedTap(false))
            ->get();

        /** @var Comment $first */
        $first = $comments->first();

        $this->assertCount(1, $comments->toArray());
        $this->assertEquals($unapprovedId, $first->getId());
        $this->assertEquals('0', $first->getCommentApproved());
    }

    /**
     * @return void
     * @covers IsApprovedTap::__construct
     */
    public function testDefaultsToApprovedWhenNoParameterProvided(): void
    {
        $approvedId = self::factory()->comment->create([
            'comment_approved' => '1',
            'comment_content' => 'Approved comment',
        ]);

        self::factory()->comment->create([
            'comment_approved' => '0',
            'comment_content' => 'Unapproved comment',
        ]);

        $comments = Comment::query()
            ->tap(new IsApprovedTap())
            ->get();

        /** @var Comment $first */
        $first = $comments->first();

        $this->assertCount(1, $comments->toArray());
        $this->assertEquals($approvedId, $first->getId());
        $this->assertEquals('1', $first->getCommentApproved());
    }

    /**
     * @return void
     * @covers IsApprovedTap::__invoke
     */
    public function testReturnsMultipleApprovedComments(): void
    {
        $approvedIds = [];
        $approvedIds[] = self::factory()->comment->create([
            'comment_approved' => '1',
            'comment_content' => 'First approved',
        ]);
        $approvedIds[] = self::factory()->comment->create([
            'comment_approved' => '1',
            'comment_content' => 'Second approved',
        ]);
        $approvedIds[] = self::factory()->comment->create([
            'comment_approved' => '1',
            'comment_content' => 'Third approved',
        ]);

        self::factory()->comment->create([
            'comment_approved' => '0',
            'comment_content' => 'Unapproved comment',
        ]);
        self::factory()->comment->create([
            'comment_approved' => 'spam',
            'comment_content' => 'Spam comment',
        ]);

        $comments = Comment::query()
            ->tap(new IsApprovedTap(true))
            ->get();

        $this->assertCount(3, $comments->toArray());
        $this->assertEquals($approvedIds, $comments->pluck('comment_ID')->toArray());
    }

    /**
     * @return void
     * @covers IsApprovedTap::__invoke
     */
    public function testReturnsMultipleUnapprovedComments(): void
    {
        self::factory()->comment->create([
            'comment_approved' => '1',
            'comment_content' => 'Approved comment',
        ]);

        $unapprovedIds = [];
        $unapprovedIds[] = self::factory()->comment->create([
            'comment_approved' => '0',
            'comment_content' => 'First unapproved',
        ]);
        $unapprovedIds[] = self::factory()->comment->create([
            'comment_approved' => '0',
            'comment_content' => 'Second unapproved',
        ]);

        $comments = Comment::query()
            ->tap(new IsApprovedTap(false))
            ->get();

        $this->assertCount(2, $comments->toArray());
        $this->assertEquals($unapprovedIds, $comments->pluck('comment_ID')->toArray());
    }

    /**
     * @return void
     * @covers IsApprovedTap::__invoke
     */
    public function testReturnsEmptyCollectionWhenNoCommentsMatch(): void
    {
        self::factory()->comment->create([
            'comment_approved' => '1',
            'comment_content' => 'Approved comment',
        ]);

        self::factory()->comment->create([
            'comment_approved' => '1',
            'comment_content' => 'Another approved',
        ]);

        $comments = Comment::query()
            ->tap(new IsApprovedTap(false))
            ->get();

        $this->assertCount(0, $comments->toArray());
    }

    /**
     * @return void
     * @covers IsApprovedTap::__invoke
     */
    public function testCanBeChainedWithOtherQueryMethods(): void
    {
        $postId = self::factory()->post->create();

        self::factory()->comment->create([
            'comment_approved' => '1',
            'comment_post_ID' => $postId,
            'comment_content' => 'First approved for post',
        ]);

        $secondApprovedId = self::factory()->comment->create([
            'comment_approved' => '1',
            'comment_post_ID' => $postId,
            'comment_author' => 'John Doe',
            'comment_content' => 'Second approved for post',
        ]);

        self::factory()->comment->create([
            'comment_approved' => '0',
            'comment_post_ID' => $postId,
            'comment_content' => 'Unapproved for post',
        ]);

        $comments = Comment::query()
            ->tap(new IsApprovedTap(true))
            ->where('comment_author', 'John Doe')
            ->get();

        /** @var Comment $first */
        $first = $comments->first();

        $this->assertCount(1, $comments->toArray());
        $this->assertEquals($secondApprovedId, $first->getId());
        $this->assertEquals('1', $first->getCommentApproved());
        $this->assertEquals('John Doe', $first->getCommentAuthor());
    }

    /**
     * @return void
     * @covers IsApprovedTap::__invoke
     */
    public function testCanBeChainedWithWhereClauseForPostId(): void
    {
        $postId = self::factory()->post->create();
        $otherPostId = self::factory()->post->create();

        $expectedId = self::factory()->comment->create([
            'comment_approved' => '1',
            'comment_post_ID' => $postId,
            'comment_content' => 'Approved for specific post',
        ]);

        self::factory()->comment->create([
            'comment_approved' => '1',
            'comment_post_ID' => $otherPostId,
            'comment_content' => 'Approved for other post',
        ]);

        self::factory()->comment->create([
            'comment_approved' => '0',
            'comment_post_ID' => $postId,
            'comment_content' => 'Unapproved for specific post',
        ]);

        $comments = Comment::query()
            ->tap(new IsApprovedTap(true))
            ->where('comment_post_ID', $postId)
            ->get();

        /** @var Comment $first */
        $first = $comments->first();

        $this->assertCount(1, $comments->toArray());
        $this->assertEquals($expectedId, $first->getId());
        $this->assertEquals('1', $first->getCommentApproved());
        $this->assertEquals($postId, $first->getCommentPostID());
    }

    /**
     * @return void
     * @covers IsApprovedTap::__invoke
     */
    public function testGeneratesCorrectSqlQueryForApproved(): void
    {
        Comment::query()
            ->tap(new IsApprovedTap(true))
            ->get();

        $this->assertLastQueryEquals(
            "select `#TABLE_PREFIX#comments`.* from `#TABLE_PREFIX#comments` where `comment_approved` = 1"
        );
    }

    /**
     * @return void
     * @covers IsApprovedTap::__invoke
     */
    public function testGeneratesCorrectSqlQueryForUnapproved(): void
    {
        Comment::query()
            ->tap(new IsApprovedTap(false))
            ->get();

        $this->assertLastQueryEquals(
            "select `#TABLE_PREFIX#comments`.* from `#TABLE_PREFIX#comments` where `comment_approved` = 0"
        );
    }
}
