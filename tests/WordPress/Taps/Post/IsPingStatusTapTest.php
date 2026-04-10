<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Taps\Post;

use Dbout\WpOrm\Enums\PingStatus;
use Dbout\WpOrm\Models\Post;
use Dbout\WpOrm\Taps\Post\IsPingStatusTap;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class IsPingStatusTapTest extends TestCase
{
    /**
     * @return void
     * @covers IsPingStatusTap::__construct
     * @covers IsPingStatusTap::__invoke
     */
    public function testFiltersByOpenStatusWithEnum(): void
    {
        $openId = self::factory()->post->create([
            'post_type' => 'post',
            'post_title' => 'Post with open pings',
            'ping_status' => 'open',
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'post_title' => 'Post with closed pings',
            'ping_status' => 'closed',
        ]);

        $posts = Post::query()
            ->tap(new IsPingStatusTap(PingStatus::Open))
            ->get();

        /** @var Post $first */
        $first = $posts->first();

        $this->assertCount(1, $posts->toArray());
        $this->assertEquals($openId, $first->getId());
        $this->assertEquals('open', $first->getPingStatus());
    }

    /**
     * @return void
     * @covers IsPingStatusTap::__invoke
     */
    public function testFiltersByClosedStatusWithEnum(): void
    {
        self::factory()->post->create([
            'post_type' => 'post',
            'post_title' => 'Post with open pings',
            'ping_status' => 'open',
        ]);

        $closedId = self::factory()->post->create([
            'post_type' => 'post',
            'post_title' => 'Post with closed pings',
            'ping_status' => 'closed',
        ]);

        $posts = Post::query()
            ->tap(new IsPingStatusTap(PingStatus::Closed))
            ->get();

        /** @var Post $first */
        $first = $posts->first();

        $this->assertCount(1, $posts->toArray());
        $this->assertEquals($closedId, $first->getId());
        $this->assertEquals('closed', $first->getPingStatus());
    }

    /**
     * @return void
     * @covers IsPingStatusTap::__invoke
     */
    public function testFiltersByOpenStatusWithString(): void
    {
        $openId = self::factory()->post->create([
            'post_type' => 'post',
            'post_title' => 'Post with open pings',
            'ping_status' => 'open',
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'post_title' => 'Post with closed pings',
            'ping_status' => 'closed',
        ]);

        $posts = Post::query()
            ->tap(new IsPingStatusTap('open'))
            ->get();

        /** @var Post $first */
        $first = $posts->first();

        $this->assertCount(1, $posts->toArray());
        $this->assertEquals($openId, $first->getId());
        $this->assertEquals('open', $first->getPingStatus());
    }

    /**
     * @return void
     * @covers IsPingStatusTap::__invoke
     */
    public function testFiltersByClosedStatusWithString(): void
    {
        self::factory()->post->create([
            'post_type' => 'post',
            'post_title' => 'Post with open pings',
            'ping_status' => 'open',
        ]);

        $closedId = self::factory()->post->create([
            'post_type' => 'post',
            'post_title' => 'Post with closed pings',
            'ping_status' => 'closed',
        ]);

        $posts = Post::query()
            ->tap(new IsPingStatusTap('closed'))
            ->get();

        /** @var Post $first */
        $first = $posts->first();

        $this->assertCount(1, $posts->toArray());
        $this->assertEquals($closedId, $first->getId());
        $this->assertEquals('closed', $first->getPingStatus());
    }

    /**
     * @return void
     * @covers IsPingStatusTap::__invoke
     */
    public function testReturnsMultiplePostsWithOpenStatus(): void
    {
        $openIds = [];
        $openIds[] = self::factory()->post->create([
            'post_type' => 'post',
            'post_title' => 'Post 1 with open pings',
            'ping_status' => 'open',
        ]);
        $openIds[] = self::factory()->post->create([
            'post_type' => 'post',
            'post_title' => 'Post 2 with open pings',
            'ping_status' => 'open',
        ]);
        $openIds[] = self::factory()->post->create([
            'post_type' => 'post',
            'post_title' => 'Post 3 with open pings',
            'ping_status' => 'open',
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'post_title' => 'Post with closed pings',
            'ping_status' => 'closed',
        ]);

        $posts = Post::query()
            ->tap(new IsPingStatusTap(PingStatus::Open))
            ->get();

        $this->assertCount(3, $posts->toArray());
        $this->assertEquals($openIds, $posts->pluck('ID')->toArray());
    }

    /**
     * @return void
     * @covers IsPingStatusTap::__invoke
     */
    public function testReturnsMultiplePostsWithClosedStatus(): void
    {
        self::factory()->post->create([
            'post_type' => 'post',
            'post_title' => 'Post with open pings',
            'ping_status' => 'open',
        ]);

        $closedIds = [];
        $closedIds[] = self::factory()->post->create([
            'post_type' => 'post',
            'post_title' => 'Post 1 with closed pings',
            'ping_status' => 'closed',
        ]);
        $closedIds[] = self::factory()->post->create([
            'post_type' => 'post',
            'post_title' => 'Post 2 with closed pings',
            'ping_status' => 'closed',
        ]);

        $posts = Post::query()
            ->tap(new IsPingStatusTap(PingStatus::Closed))
            ->get();

        $this->assertCount(2, $posts->toArray());
        $this->assertEquals($closedIds, $posts->pluck('ID')->toArray());
    }

    /**
     * @return void
     * @covers IsPingStatusTap::__invoke
     */
    public function testReturnsEmptyCollectionWhenNoPostsMatch(): void
    {
        self::factory()->post->create([
            'post_type' => 'post',
            'post_title' => 'Post with open pings',
            'ping_status' => 'open',
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'post_title' => 'Another post with open pings',
            'ping_status' => 'open',
        ]);

        $posts = Post::query()
            ->tap(new IsPingStatusTap(PingStatus::Closed))
            ->get();

        $this->assertCount(0, $posts->toArray());
    }

    /**
     * @return void
     * @covers IsPingStatusTap::__invoke
     */
    public function testCanBeChainedWithPostTypeFilter(): void
    {
        self::factory()->post->create([
            'post_type' => 'post',
            'ping_status' => 'open',
            'post_title' => 'Blog post with open pings',
        ]);

        $expectedId = self::factory()->post->create([
            'post_type' => 'page',
            'ping_status' => 'open',
            'post_title' => 'Page with open pings',
        ]);

        self::factory()->post->create([
            'post_type' => 'page',
            'ping_status' => 'closed',
            'post_title' => 'Page with closed pings',
        ]);

        $posts = Post::query()
            ->tap(new IsPingStatusTap(PingStatus::Open))
            ->where('post_type', 'page')
            ->get();

        /** @var Post $first */
        $first = $posts->first();

        $this->assertCount(1, $posts->toArray());
        $this->assertEquals($expectedId, $first->getId());
        $this->assertEquals('open', $first->getPingStatus());
        $this->assertEquals('page', $first->getPostType());
    }

    /**
     * @return void
     * @covers IsPingStatusTap::__invoke
     */
    public function testGeneratesCorrectSqlQueryForOpenWithEnum(): void
    {
        Post::query()
            ->tap(new IsPingStatusTap(PingStatus::Open))
            ->get();

        $this->assertLastQueryEquals(
            "select `#TABLE_PREFIX#posts`.* from `#TABLE_PREFIX#posts` where `ping_status` = 'open'"
        );
    }

    /**
     * @return void
     * @covers IsPingStatusTap::__invoke
     */
    public function testGeneratesCorrectSqlQueryForClosedWithEnum(): void
    {
        Post::query()
            ->tap(new IsPingStatusTap(PingStatus::Closed))
            ->get();

        $this->assertLastQueryEquals(
            "select `#TABLE_PREFIX#posts`.* from `#TABLE_PREFIX#posts` where `ping_status` = 'closed'"
        );
    }

    /**
     * @return void
     * @covers IsPingStatusTap::__invoke
     */
    public function testGeneratesCorrectSqlQueryForOpenWithString(): void
    {
        Post::query()
            ->tap(new IsPingStatusTap('open'))
            ->get();

        $this->assertLastQueryEquals(
            "select `#TABLE_PREFIX#posts`.* from `#TABLE_PREFIX#posts` where `ping_status` = 'open'"
        );
    }

    /**
     * @return void
     * @covers IsPingStatusTap::__invoke
     */
    public function testGeneratesCorrectSqlQueryForClosedWithString(): void
    {
        Post::query()
            ->tap(new IsPingStatusTap('closed'))
            ->get();

        $this->assertLastQueryEquals(
            "select `#TABLE_PREFIX#posts`.* from `#TABLE_PREFIX#posts` where `ping_status` = 'closed'"
        );
    }

    /**
     * @return void
     * @covers IsPingStatusTap::__invoke
     */
    public function testStringAndEnumProduceSameResults(): void
    {
        self::factory()->post->create([
            'post_type' => 'post',
            'ping_status' => 'open',
            'post_title' => 'Post 1',
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'ping_status' => 'open',
            'post_title' => 'Post 2',
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'ping_status' => 'closed',
            'post_title' => 'Post 3',
        ]);

        $withEnum = Post::query()
            ->tap(new IsPingStatusTap(PingStatus::Open))
            ->get();

        $withString = Post::query()
            ->tap(new IsPingStatusTap('open'))
            ->get();

        $this->assertCount(2, $withEnum->toArray());
        $this->assertCount(2, $withString->toArray());
        $this->assertEquals(
            $withEnum->pluck('ID')->toArray(),
            $withString->pluck('ID')->toArray()
        );
    }
}
