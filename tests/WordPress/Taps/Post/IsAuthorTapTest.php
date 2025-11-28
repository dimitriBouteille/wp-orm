<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Taps\Post;

use Dbout\WpOrm\Models\Post;
use Dbout\WpOrm\Models\User;
use Dbout\WpOrm\Taps\Post\IsAuthorTap;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class IsAuthorTapTest extends TestCase
{
    private const EMAIL_JOHN = 'john@example.com';

    /**
     * @return void
     * @covers IsAuthorTap::__construct
     * @covers IsAuthorTap::__invoke
     */
    public function testFiltersByAuthorIdAsInteger(): void
    {
        $author1 = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => self::EMAIL_JOHN,
        ]);

        $author2 = self::factory()->user->create([
            'user_login' => 'jane_doe',
            'user_email' => 'jane@example.com',
        ]);

        $expectedId = self::factory()->post->create([
            'post_type' => 'post',
            'post_author' => $author1,
            'post_title' => 'Post by John',
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'post_author' => $author2,
            'post_title' => 'Post by Jane',
        ]);

        $posts = Post::query()
            ->tap(new IsAuthorTap($author1))
            ->get();

        /** @var Post $first */
        $first = $posts->first();

        $this->assertCount(1, $posts->toArray());
        $this->assertEquals($expectedId, $first->getId());
        $this->assertEquals($author1, $first->getPostAuthor());
    }

    /**
     * @return void
     * @covers IsAuthorTap::__invoke
     */
    public function testFiltersByUserModel(): void
    {
        $author1 = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => self::EMAIL_JOHN,
        ]);

        $author2 = self::factory()->user->create([
            'user_login' => 'jane_doe',
            'user_email' => 'jane@example.com',
        ]);

        $expectedId = self::factory()->post->create([
            'post_type' => 'post',
            'post_author' => $author1,
            'post_title' => 'Post by John',
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'post_author' => $author2,
            'post_title' => 'Post by Jane',
        ]);

        $user = User::find($author1);

        $posts = Post::query()
            ->tap(new IsAuthorTap($user))
            ->get();

        /** @var Post $first */
        $first = $posts->first();

        $this->assertCount(1, $posts->toArray());
        $this->assertEquals($expectedId, $first->getId());
        $this->assertEquals($author1, $first->getPostAuthor());
    }

    /**
     * @return void
     * @covers IsAuthorTap::__invoke
     */
    public function testReturnsMultiplePostsFromSameAuthor(): void
    {
        $author1 = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => self::EMAIL_JOHN,
        ]);

        $author2 = self::factory()->user->create([
            'user_login' => 'jane_doe',
            'user_email' => 'jane@example.com',
        ]);

        $expectedIds = [];
        $expectedIds[] = self::factory()->post->create([
            'post_type' => 'post',
            'post_author' => $author1,
            'post_title' => 'First post by John',
        ]);
        $expectedIds[] = self::factory()->post->create([
            'post_type' => 'post',
            'post_author' => $author1,
            'post_title' => 'Second post by John',
        ]);
        $expectedIds[] = self::factory()->post->create([
            'post_type' => 'post',
            'post_author' => $author1,
            'post_title' => 'Third post by John',
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'post_author' => $author2,
            'post_title' => 'Post by Jane',
        ]);

        $posts = Post::query()
            ->tap(new IsAuthorTap($author1))
            ->get();

        $this->assertCount(3, $posts->toArray());
        $this->assertEquals($expectedIds, $posts->pluck('ID')->toArray());
    }

    /**
     * @return void
     * @covers IsAuthorTap::__invoke
     */
    public function testReturnsEmptyCollectionWhenAuthorHasNoPosts(): void
    {
        $author1 = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => self::EMAIL_JOHN,
        ]);

        $author2 = self::factory()->user->create([
            'user_login' => 'jane_doe',
            'user_email' => 'jane@example.com',
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'post_author' => $author2,
            'post_title' => 'Post by Jane',
        ]);

        $posts = Post::query()
            ->tap(new IsAuthorTap($author1))
            ->get();

        $this->assertCount(0, $posts->toArray());
    }

    /**
     * @return void
     * @covers IsAuthorTap::__invoke
     */
    public function testCanBeChainedWithPostTypeFilter(): void
    {
        $author = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => self::EMAIL_JOHN,
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'post_author' => $author,
            'post_title' => 'Blog post by John',
        ]);

        $expectedId = self::factory()->post->create([
            'post_type' => 'product',
            'post_author' => $author,
            'post_title' => 'Product by John',
        ]);

        self::factory()->post->create([
            'post_type' => 'product',
            'post_author' => 0,
            'post_title' => 'Product without author',
        ]);

        $posts = Post::query()
            ->tap(new IsAuthorTap($author))
            ->where('post_type', 'product')
            ->get();

        /** @var Post $first */
        $first = $posts->first();

        $this->assertCount(1, $posts->toArray());
        $this->assertEquals($expectedId, $first->getId());
        $this->assertEquals($author, $first->getPostAuthor());
        $this->assertEquals('product', $first->getPostType());
    }

    /**
     * @return void
     * @covers IsAuthorTap::__invoke
     */
    public function testGeneratesCorrectSqlQueryWithInteger(): void
    {
        $authorId = 42;

        Post::query()
            ->tap(new IsAuthorTap($authorId))
            ->get();

        $this->assertLastQueryEquals(
            "select `#TABLE_PREFIX#posts`.* from `#TABLE_PREFIX#posts` where `post_author` = 42"
        );
    }

    /**
     * @return void
     * @covers IsAuthorTap::__invoke
     */
    public function testGeneratesCorrectSqlQueryWithUserModel(): void
    {
        $authorId = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => self::EMAIL_JOHN,
        ]);

        $user = User::find($authorId);

        Post::query()
            ->tap(new IsAuthorTap($user))
            ->get();

        $this->assertLastQueryEquals(
            sprintf("select `#TABLE_PREFIX#posts`.* from `#TABLE_PREFIX#posts` where `post_author` = %d", $authorId)
        );
    }

    /**
     * @return void
     * @covers IsAuthorTap::__invoke
     */
    public function testDistinguishesBetweenDifferentAuthors(): void
    {
        $author1 = self::factory()->user->create([
            'user_login' => 'author1',
            'user_email' => 'author1@example.com',
        ]);

        $author2 = self::factory()->user->create([
            'user_login' => 'author2',
            'user_email' => 'author2@example.com',
        ]);

        $author3 = self::factory()->user->create([
            'user_login' => 'author3',
            'user_email' => 'author3@example.com',
        ]);

        $expectedId = self::factory()->post->create([
            'post_type' => 'post',
            'post_author' => $author2,
            'post_title' => 'Post by author 2',
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'post_author' => $author1,
            'post_title' => 'Post by author 1',
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'post_author' => $author3,
            'post_title' => 'Post by author 3',
        ]);

        $posts = Post::query()
            ->tap(new IsAuthorTap($author2))
            ->get();

        /** @var Post $first */
        $first = $posts->first();

        $this->assertCount(1, $posts->toArray());
        $this->assertEquals($expectedId, $first->getId());
        $this->assertEquals($author2, $first->getPostAuthor());
    }

    /**
     * @return void
     * @covers IsAuthorTap::__invoke
     */
    public function testWorksWithDifferentPostTypes(): void
    {
        $author = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => self::EMAIL_JOHN,
        ]);

        $postId = self::factory()->post->create([
            'post_type' => 'post',
            'post_author' => $author,
            'post_title' => 'Blog post',
        ]);

        $pageId = self::factory()->post->create([
            'post_type' => 'page',
            'post_author' => $author,
            'post_title' => 'Page',
        ]);

        $productId = self::factory()->post->create([
            'post_type' => 'product',
            'post_author' => $author,
            'post_title' => 'Product',
        ]);

        $posts = Post::query()
            ->tap(new IsAuthorTap($author))
            ->get();

        $this->assertCount(3, $posts->toArray());
        $ids = $posts->pluck('ID')->toArray();
        $this->assertEqualsCanonicalizing([$postId, $pageId, $productId], $ids);
    }

    /**
     * @return void
     * @covers IsAuthorTap::__invoke
     */
    public function testFiltersPostsByZeroAuthor(): void
    {
        $author = self::factory()->user->create([
            'user_login' => 'john_doe',
            'user_email' => self::EMAIL_JOHN,
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
            'post_author' => $author,
            'post_title' => 'Post with author',
        ]);

        $expectedId = self::factory()->post->create([
            'post_type' => 'post',
            'post_author' => 0,
            'post_title' => 'Post without author',
        ]);

        $posts = Post::query()
            ->tap(new IsAuthorTap(0))
            ->get();

        /** @var Post $first */
        $first = $posts->first();

        $this->assertCount(1, $posts->toArray());
        $this->assertEquals($expectedId, $first->getId());
        $this->assertEquals(0, $first->getPostAuthor());
    }
}
