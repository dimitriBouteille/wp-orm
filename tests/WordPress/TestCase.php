<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress;

use Dbout\WpOrm\Models\Comment;
use Dbout\WpOrm\Models\Post;

/**
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
 * @method static mixed factory()
 */
abstract class TestCase extends \WP_UnitTestCase
{
    /**
     * @param string $query
     * @return void
     */
    public function assertLastQueryEqual(string $query): void
    {
        global $wpdb;
        self::assertEquals($query, $wpdb->last_query);
    }

    /**
     * @param Post|null $model
     * @param \WP_Post|null $wpPost
     * @return void
     */
    public function assertPostEqualToWpObject(?Post $model, ?\WP_Post $wpPost): void
    {
        self::assertInstanceOf(\WP_Post::class, $wpPost);

        self::assertEquals($wpPost->ID, $model->getId());
        self::assertEquals($wpPost->post_type, $model->getPostType());
        self::assertEquals($wpPost->post_content, $model->getPostContent());
        self::assertEquals($wpPost->post_title, $model->getPostTitle());
        self::assertEquals($wpPost->post_name, $model->getPostName());
    }

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
        $this->assertEquals($wpPost->post_status, $post->getPostStatus());
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
     * @param string $table
     * @param string $whereColumn
     * @param string $whereValue
     * @return void
     */
    protected function assertFindLastQuery(string $table, string $whereColumn, string $whereValue): void
    {
        $this->assertLastQueryEquals(
            sprintf(
                "select `#TABE_PREFIX#%s`.* from `#TABLE_PREFIX#%s` where `%s` = '%s' limit 1",
                $table,
                $table,
                $whereColumn,
                $whereValue
            )
        );
    }

    /**
     * @param string $query
     * @param string $message
     * @return void
     */
    public function assertLastQueryEquals(string $query, string $message = ''): void
    {
        global $wpdb;
        $query = str_replace('#TABLE_PREFIX#', $wpdb->prefix, $query);
        self::assertEquals($query, $wpdb->last_query, $message);
    }
}
