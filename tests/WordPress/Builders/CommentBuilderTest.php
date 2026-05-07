<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Builders;

use Dbout\WpOrm\Builders\CommentBuilder;
use Dbout\WpOrm\Models\Comment;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class CommentBuilderTest extends TestCase
{
    /**
     * @return void
     * @covers CommentBuilder::findAllByType
     */
    public function testFindAllByTypeReturnsOnlyMatchingType(): void
    {
        $reviewId = self::factory()->comment->create([
            'comment_type'    => 'review',
            'comment_content' => 'Review comment',
        ]);

        self::factory()->comment->create([
            'comment_type'    => 'order_note',
            'comment_content' => 'Order note',
        ]);

        $comments = Comment::query()->findAllByType('review');

        /** @var Comment $first */
        $first = $comments->first();

        $this->assertCount(1, $comments->toArray());
        $this->assertEquals($reviewId, $first->getId());
        $this->assertEquals('review', $first->getCommentType());
    }

    /**
     * @return void
     * @covers CommentBuilder::findAllByType
     */
    public function testFindAllByTypeReturnsEmptyWhenNoMatch(): void
    {
        self::factory()->comment->create([
            'comment_type'    => 'review',
            'comment_content' => 'Review comment',
        ]);

        $comments = Comment::query()->findAllByType('unknown_type');

        $this->assertCount(0, $comments->toArray());
    }

    /**
     * @return void
     * @covers CommentBuilder::whereTypes
     */
    public function testWhereTypesWithSingleType(): void
    {
        $reviewId = self::factory()->comment->create([
            'comment_type'    => 'review',
            'comment_content' => 'Review comment',
        ]);

        self::factory()->comment->create([
            'comment_type'    => 'order_note',
            'comment_content' => 'Order note',
        ]);

        $comments = Comment::query()
            ->whereTypes('review')
            ->get();

        /** @var Comment $first */
        $first = $comments->first();

        $this->assertCount(1, $comments->toArray());
        $this->assertEquals($reviewId, $first->getId());
    }

    /**
     * @return void
     * @covers CommentBuilder::whereTypes
     */
    public function testWhereTypesWithMultipleVariadicArgs(): void
    {
        $reviewId = self::factory()->comment->create([
            'comment_type'    => 'review',
            'comment_content' => 'Review comment',
        ]);

        $orderNoteId = self::factory()->comment->create([
            'comment_type'    => 'order_note',
            'comment_content' => 'Order note',
        ]);

        self::factory()->comment->create([
            'comment_type'    => 'pingback',
            'comment_content' => 'Pingback',
        ]);

        $comments = Comment::query()
            ->whereTypes('review', 'order_note')
            ->get();

        $ids = $comments->pluck(Comment::COMMENT_ID)->toArray();

        $this->assertCount(2, $comments->toArray());
        $this->assertEqualsCanonicalizing([$reviewId, $orderNoteId], $ids);
    }

    /**
     * @return void
     * @covers CommentBuilder::whereTypes
     */
    public function testWhereTypesAcceptsArrayAsFirstArgument(): void
    {
        $reviewId = self::factory()->comment->create([
            'comment_type'    => 'review',
            'comment_content' => 'Review comment',
        ]);

        $orderNoteId = self::factory()->comment->create([
            'comment_type'    => 'order_note',
            'comment_content' => 'Order note',
        ]);

        self::factory()->comment->create([
            'comment_type'    => 'pingback',
            'comment_content' => 'Pingback',
        ]);

        $comments = Comment::query()
            ->whereTypes(['review', 'order_note'])
            ->get();

        $ids = $comments->pluck(Comment::COMMENT_ID)->toArray();

        $this->assertCount(2, $comments->toArray());
        $this->assertEqualsCanonicalizing([$reviewId, $orderNoteId], $ids);
    }

    /**
     * @return void
     * @covers CommentBuilder::whereTypes
     */
    public function testWhereTypesCanBeChained(): void
    {
        $postId      = self::factory()->post->create();
        $otherPostId = self::factory()->post->create();

        $expectedId = self::factory()->comment->create([
            'comment_type'    => 'review',
            'comment_post_ID' => $postId,
            'comment_content' => 'Review on target post',
        ]);

        self::factory()->comment->create([
            'comment_type'    => 'review',
            'comment_post_ID' => $otherPostId,
            'comment_content' => 'Review on other post',
        ]);

        self::factory()->comment->create([
            'comment_type'    => 'order_note',
            'comment_post_ID' => $postId,
            'comment_content' => 'Order note on target post',
        ]);

        $comments = Comment::query()
            ->whereTypes('review')
            ->where(Comment::POST_ID, $postId)
            ->get();

        /** @var Comment $first */
        $first = $comments->first();

        $this->assertCount(1, $comments->toArray());
        $this->assertEquals($expectedId, $first->getId());
    }
}
