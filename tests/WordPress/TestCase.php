<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress;

use Dbout\WpOrm\Models\Comment;
use Dbout\WpOrm\Models\Post;
use Illuminate\Support\Collection;

/**
 * @method static|$this assertNotNull(mixed $object, string $message = '')
 * @method static|$this assertEquals(mixed $expectedValue, mixed $checkValue, string $message = '')
 * @method static|$this assertInstanceOf(string $className, mixed $object, string $message = '')
 * @method static|$this expectExceptionMessageMatches(string $pattern, string $message = '')
 * @method static|$this expectException(string $className, string $message = '')
 * @method static|$this assertTrue(mixed $value, string $message = '')
 * @method static|$this assertFalse(mixed $value, string $message = '')
 * @method static|$this assertNull(mixed $value, string $message = '')
 * @method static|$this assertCount(int $expectedCount, array $array, string $message = '')
 * @method static|$this assertIsNumeric(mixed $vale, string $message = '')
 * @method static|$this assertEqualsCanonicalizing(mixed $expected, mixed $actual, string $message = '')
 * @method static|$this assertSame(mixed $expected, mixed $actual, string $message = '')
 * @method static|$this assertIsArray(mixed $value, string $message = '')
 * @method static|$this assertContains(mixed $needle, array $haystack, string $message = '')
 * @method static|$this assertNotContains(mixed $needle, array $haystack, string $message = '')
 * @method static mixed factory()
 */
abstract class TestCase extends \WP_UnitTestCase
{
    /**
     * @param int|null $id
     * @return void
     */
    public function assertEqualLastInsertId(?int $id): void
    {
        global $wpdb;
        self::assertEquals($wpdb->insert_id, $id);
    }

    /**
     * @param string $columnName
     * @return void
     */
    public function expectExceptionUnknownColumn(string $columnName): void
    {
        $this->expectExceptionMessageMatches(sprintf("/^Unknown column '%s' in 'field list'/", $columnName));
    }

    /**
     * @param string $table
     * @return string
     */
    protected static function getTable(string $table): string
    {
        global $wpdb;
        return $wpdb->prefix . $table;
    }

    /**
     * @param Post $post
     * @return void
     */
    public function assertPostEqualsToWpPost(Post $post): void
    {
        $wpPost = get_post($post->getId());

        $this->assertInstanceOf(\WP_Post::class, $wpPost);
        $this->assertEquals($wpPost->ID, $post->getId());
        $this->assertEquals($wpPost->post_content, $post->getPostContent());
        $this->assertEquals($wpPost->post_type, $post->getPostType());
        $this->assertEquals($wpPost->post_title, $post->getPostTitle());
        $this->assertEquals($wpPost->post_excerpt, $post->getPostExcerpt());
        $this->assertEquals($wpPost->post_name, $post->getPostName());
    }

    /**
     * @param Comment $comment
     * @return void
     */
    public function assertCommentEqualsToWpComment(Comment $comment): void
    {
        $wpComment = get_comment($comment->getId());
        $this->assertEquals($wpComment->comment_ID, $comment->getId());
        $this->assertEquals($wpComment->comment_content, $comment->getCommentContent());
        $this->assertEquals($wpComment->comment_author_email, $comment->getCommentAuthorEmail());
        $this->assertEquals($wpComment->comment_author, $comment->getCommentAuthor());
        $this->assertEquals($wpComment->comment_type, $comment->getCommentType());
    }

    /**
     * Assert that the last executed SQL contains a substring.
     *
     * Use this only when the SQL shape is itself part of the contract
     * (custom grammar, security regression tests). For most tests, prefer
     * asserting on the result rows — see TESTS_AUDIT.md point #1.
     *
     * @param string $needle
     * @param string $message
     * @return void
     */
    public function assertLastQueryContains(string $needle, string $message = ''): void
    {
        global $wpdb;
        $needle = str_replace('#TABLE_PREFIX#', $wpdb->prefix, $needle);
        self::assertStringContainsString($needle, (string) $wpdb->last_query, $message);
    }

    /**
     * @param Collection $expectedItems
     * @param string $relationProperty
     * @param array $expectedIds
     * @return void
     */
    public function assertHasManyRelation(
        Collection $expectedItems,
        string $relationProperty,
        array $expectedIds
    ): void {
        $ids = $expectedItems->pluck($relationProperty);
        $this->assertCount(count($expectedIds), $expectedItems->toArray());
        $this->assertEqualsCanonicalizing($expectedIds, $ids->toArray());
    }
}
