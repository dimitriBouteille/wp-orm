<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Dbout\WpOrm\Tests\WordPress\Taps\Post;

use Dbout\WpOrm\Models\Post;
use Dbout\WpOrm\Taps\Post\IsPostTypeTap;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class IsPostTypeTapTest extends TestCase
{
    /**
     * @return void
     * @covers IsPostTypeTap::__construct
     * @covers IsPostTypeTap::__invoke
     */
    public function testFiltersByPostType(): void
    {
        $postId = self::factory()->post->create([
            'post_type' => 'post',
            'post_title' => 'Blog post',
        ]);

        self::factory()->post->create([
            'post_type' => 'page',
            'post_title' => 'About page',
        ]);

        self::factory()->post->create([
            'post_type' => 'product',
            'post_title' => 'Product',
        ]);

        $posts = Post::query()
            ->tap(new IsPostTypeTap('post'))
            ->get();

        /** @var Post $first */
        $first = $posts->first();

        $this->assertCount(1, $posts->toArray());
        $this->assertEquals($postId, $first->getId());
        $this->assertEquals('post', $first->getPostType());
    }

    /**
     * @return void
     * @covers IsPostTypeTap::__invoke
     */
    public function testFiltersByPageType(): void
    {
        self::factory()->post->create([
            'post_type' => 'post',
            'post_title' => 'Blog post',
        ]);

        $pageId = self::factory()->post->create([
            'post_type' => 'page',
            'post_title' => 'About page',
        ]);

        $posts = Post::query()
            ->tap(new IsPostTypeTap('page'))
            ->get();

        /** @var Post $first */
        $first = $posts->first();

        $this->assertCount(1, $posts->toArray());
        $this->assertEquals($pageId, $first->getId());
        $this->assertEquals('page', $first->getPostType());
    }

    /**
     * @return void
     * @covers IsPostTypeTap::__invoke
     */
    public function testReturnsMultiplePostsWithSameType(): void
    {
        $productIds = [];
        $productIds[] = self::factory()->post->create([
            'post_type' => 'product',
            'post_title' => 'Product 1',
        ]);
        $productIds[] = self::factory()->post->create([
            'post_type' => 'product',
            'post_title' => 'Product 2',
        ]);
        $productIds[] = self::factory()->post->create([
            'post_type' => 'product',
            'post_title' => 'Product 3',
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'post_title' => 'Blog post',
        ]);
        self::factory()->post->create([
            'post_type' => 'page',
            'post_title' => 'Page',
        ]);

        $posts = Post::query()
            ->tap(new IsPostTypeTap('product'))
            ->get();

        $this->assertCount(3, $posts->toArray());
        $this->assertEquals($productIds, $posts->pluck('ID')->toArray());
    }

    /**
     * @return void
     * @covers IsPostTypeTap::__invoke
     */
    public function testReturnsEmptyCollectionWhenNoPostsMatch(): void
    {
        self::factory()->post->create([
            'post_type' => 'post',
            'post_title' => 'Blog post',
        ]);

        self::factory()->post->create([
            'post_type' => 'page',
            'post_title' => 'Page',
        ]);

        $posts = Post::query()
            ->tap(new IsPostTypeTap('product'))
            ->get();

        $this->assertCount(0, $posts->toArray());
    }

    /**
     * @return void
     * @covers IsPostTypeTap::__invoke
     */
    public function testCanBeChainedWithStatusFilter(): void
    {
        self::factory()->post->create([
            'post_type' => 'product',
            'post_status' => 'draft',
            'post_title' => 'Draft product',
        ]);

        $publishedId = self::factory()->post->create([
            'post_type' => 'product',
            'post_status' => 'publish',
            'post_title' => 'Published product',
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'post_status' => 'publish',
            'post_title' => 'Published post',
        ]);

        $posts = Post::query()
            ->tap(new IsPostTypeTap('product'))
            ->where('post_status', 'publish')
            ->get();

        /** @var Post $first */
        $first = $posts->first();

        $this->assertCount(1, $posts->toArray());
        $this->assertEquals($publishedId, $first->getId());
        $this->assertEquals('product', $first->getPostType());
        $this->assertEquals('publish', $first->getPostStatus());
    }

    /**
     * @return void
     * @covers IsPostTypeTap::__invoke
     */
    public function testGeneratesCorrectSqlQuery(): void
    {
        Post::query()
            ->tap(new IsPostTypeTap('product'))
            ->get();

        $this->assertLastQueryEquals(
            "select `#TABLE_PREFIX#posts`.* from `#TABLE_PREFIX#posts` where `post_type` = 'product'"
        );
    }

    /**
     * @return void
     * @covers IsPostTypeTap::__invoke
     */
    public function testDistinguishesBetweenDifferentPostTypes(): void
    {
        self::factory()->post->create([
            'post_type' => 'post',
            'post_title' => 'Blog post',
        ]);

        self::factory()->post->create([
            'post_type' => 'page',
            'post_title' => 'Page',
        ]);

        $productId = self::factory()->post->create([
            'post_type' => 'product',
            'post_title' => 'Product',
        ]);

        self::factory()->post->create([
            'post_type' => 'event',
            'post_title' => 'Event',
        ]);

        $posts = Post::query()
            ->tap(new IsPostTypeTap('product'))
            ->get();

        /** @var Post $first */
        $first = $posts->first();

        $this->assertCount(1, $posts->toArray());
        $this->assertEquals($productId, $first->getId());
        $this->assertEquals('product', $first->getPostType());
    }
}
