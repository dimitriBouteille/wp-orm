<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Dbout\WpOrm\Tests\WordPress\Taps\Post;

use Dbout\WpOrm\Enums\PostStatus;
use Dbout\WpOrm\Models\Post;
use Dbout\WpOrm\Taps\Post\IsStatusTap;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class IsStatusTapTest extends TestCase
{
    /**
     * @return void
     * @covers IsStatusTap::__construct
     * @covers IsStatusTap::__invoke
     */
    public function testFiltersByPublishStatusWithEnum(): void
    {
        $publishedId = self::factory()->post->create([
            'post_type' => 'post',
            'post_status' => 'publish',
            'post_title' => 'Published post',
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'post_status' => 'draft',
            'post_title' => 'Draft post',
        ]);

        $posts = Post::query()
            ->tap(new IsStatusTap(PostStatus::Publish))
            ->get();

        /** @var Post $first */
        $first = $posts->first();

        $this->assertCount(1, $posts->toArray());
        $this->assertEquals($publishedId, $first->getId());
        $this->assertEquals('publish', $first->getPostStatus());
    }

    /**
     * @return void
     * @covers IsStatusTap::__invoke
     */
    public function testFiltersByPublishStatusWithString(): void
    {
        $publishedId = self::factory()->post->create([
            'post_type' => 'post',
            'post_status' => 'publish',
            'post_title' => 'Published post',
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'post_status' => 'draft',
            'post_title' => 'Draft post',
        ]);

        $posts = Post::query()
            ->tap(new IsStatusTap('publish'))
            ->get();

        /** @var Post $first */
        $first = $posts->first();

        $this->assertCount(1, $posts->toArray());
        $this->assertEquals($publishedId, $first->getId());
        $this->assertEquals('publish', $first->getPostStatus());
    }

    /**
     * @return void
     * @covers IsStatusTap::__invoke
     */
    public function testReturnsMultiplePostsWithPublishStatus(): void
    {
        $publishedIds = [];
        $publishedIds[] = self::factory()->post->create([
            'post_type' => 'post',
            'post_status' => 'publish',
            'post_title' => 'Published post 1',
        ]);
        $publishedIds[] = self::factory()->post->create([
            'post_type' => 'post',
            'post_status' => 'publish',
            'post_title' => 'Published post 2',
        ]);
        $publishedIds[] = self::factory()->post->create([
            'post_type' => 'post',
            'post_status' => 'publish',
            'post_title' => 'Published post 3',
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'post_status' => 'draft',
            'post_title' => 'Draft post',
        ]);

        $posts = Post::query()
            ->tap(new IsStatusTap(PostStatus::Publish))
            ->get();

        $this->assertCount(3, $posts->toArray());
        $this->assertEquals($publishedIds, $posts->pluck('ID')->toArray());
    }

    /**
     * @return void
     * @covers IsStatusTap::__invoke
     */
    public function testReturnsEmptyCollectionWhenNoPostsMatch(): void
    {
        self::factory()->post->create([
            'post_type' => 'post',
            'post_status' => 'publish',
            'post_title' => 'Published post',
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'post_status' => 'draft',
            'post_title' => 'Draft post',
        ]);

        $posts = Post::query()
            ->tap(new IsStatusTap(PostStatus::Pending))
            ->get();

        $this->assertCount(0, $posts->toArray());
    }

    /**
     * @return void
     * @covers IsStatusTap::__invoke
     */
    public function testCanBeChainedWithPostTypeFilter(): void
    {
        self::factory()->post->create([
            'post_type' => 'post',
            'post_status' => 'publish',
            'post_title' => 'Published blog post',
        ]);

        $expectedId = self::factory()->post->create([
            'post_type' => 'product',
            'post_status' => 'publish',
            'post_title' => 'Published product',
        ]);

        self::factory()->post->create([
            'post_type' => 'product',
            'post_status' => 'draft',
            'post_title' => 'Draft product',
        ]);

        $posts = Post::query()
            ->tap(new IsStatusTap(PostStatus::Publish))
            ->where('post_type', 'product')
            ->get();

        /** @var Post $first */
        $first = $posts->first();

        $this->assertCount(1, $posts->toArray());
        $this->assertEquals($expectedId, $first->getId());
        $this->assertEquals('publish', $first->getPostStatus());
        $this->assertEquals('product', $first->getPostType());
    }

    /**
     * @return void
     * @covers IsStatusTap::__invoke
     */
    public function testGeneratesCorrectSqlQueryForPublishWithEnum(): void
    {
        Post::query()
            ->tap(new IsStatusTap(PostStatus::Publish))
            ->get();

        $this->assertLastQueryEquals(
            "select `#TABLE_PREFIX#posts`.* from `#TABLE_PREFIX#posts` where `post_status` = 'publish'"
        );
    }

    /**
     * @return void
     * @covers IsStatusTap::__invoke
     */
    public function testGeneratesCorrectSqlQueryForPublishWithString(): void
    {
        Post::query()
            ->tap(new IsStatusTap('publish'))
            ->get();

        $this->assertLastQueryEquals(
            "select `#TABLE_PREFIX#posts`.* from `#TABLE_PREFIX#posts` where `post_status` = 'publish'"
        );
    }

    /**
     * @return void
     * @covers IsStatusTap::__invoke
     */
    public function testDistinguishesBetweenDifferentStatuses(): void
    {
        self::factory()->post->create([
            'post_type' => 'post',
            'post_status' => 'publish',
            'post_title' => 'Published post',
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'post_status' => 'draft',
            'post_title' => 'Draft post',
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'post_status' => 'pending',
            'post_title' => 'Pending post',
        ]);

        $publishedPosts = Post::query()
            ->tap(new IsStatusTap(PostStatus::Publish))
            ->get();

        $draftPosts = Post::query()
            ->tap(new IsStatusTap(PostStatus::Draft))
            ->get();

        $pendingPosts = Post::query()
            ->tap(new IsStatusTap(PostStatus::Pending))
            ->get();

        /** @var Post $first */
        $first = $publishedPosts->first();
        $this->assertCount(1, $publishedPosts->toArray());
        $this->assertEquals('publish', $first->getPostStatus());

        /** @var Post $first */
        $first = $draftPosts->first();
        $this->assertCount(1, $draftPosts->toArray());
        $this->assertEquals('draft', $first->getPostStatus());

        /** @var Post $first */
        $first = $pendingPosts->first();
        $this->assertCount(1, $pendingPosts->toArray());
        $this->assertEquals('pending', $first->getPostStatus());
    }

    /**
     * @return void
     * @covers IsStatusTap::__invoke
     */
    public function testStringAndEnumProduceSameResults(): void
    {
        self::factory()->post->create([
            'post_type' => 'post',
            'post_status' => 'publish',
            'post_title' => 'Post 1',
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'post_status' => 'publish',
            'post_title' => 'Post 2',
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'post_status' => 'draft',
            'post_title' => 'Post 3',
        ]);

        $withEnum = Post::query()
            ->tap(new IsStatusTap(PostStatus::Publish))
            ->get();

        $withString = Post::query()
            ->tap(new IsStatusTap('publish'))
            ->get();

        $this->assertCount(2, $withEnum->toArray());
        $this->assertCount(2, $withString->toArray());
        $this->assertEquals(
            $withEnum->pluck('ID')->toArray(),
            $withString->pluck('ID')->toArray()
        );
    }

    /**
     * @return void
     * @covers IsStatusTap::__invoke
     */
    public function testWorksWithDifferentPostTypes(): void
    {
        $postId = self::factory()->post->create([
            'post_type' => 'post',
            'post_status' => 'publish',
            'post_title' => 'Blog post',
        ]);

        $pageId = self::factory()->post->create([
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_title' => 'Page',
        ]);

        $productId = self::factory()->post->create([
            'post_type' => 'product',
            'post_status' => 'publish',
            'post_title' => 'Product',
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'post_status' => 'draft',
            'post_title' => 'Draft post',
        ]);

        $posts = Post::query()
            ->tap(new IsStatusTap(PostStatus::Publish))
            ->get();

        $this->assertCount(3, $posts->toArray());
        $ids = $posts->pluck('ID')->toArray();
        $this->assertEqualsCanonicalizing([$postId, $pageId, $productId], $ids);
    }
}
