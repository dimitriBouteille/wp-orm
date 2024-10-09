<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress;

use Dbout\WpOrm\Models\Comment;
use Dbout\WpOrm\Models\Post;
use Illuminate\Support\Collection;

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
     * @param string $table
     * @param string $whereColumn
     * @param string $whereValue
     * @return void
     */
    protected function assertFindLastQuery(string $table, string $whereColumn, string $whereValue): void
    {
        $this->assertLastQueryEquals(
            sprintf(
                "select `#TABLE_PREFIX#%s`.* from `#TABLE_PREFIX#%s` where `%s` = '%s' limit 1",
                $table,
                $table,
                $whereColumn,
                $whereValue
            )
        );
    }

    /**
     * @param string $table
     * @param string $pkColum
     * @param string $pkValue
     * @return void
     */
    public function assertLastQueryHasOneRelation(string $table, string $pkColum, string $pkValue): void
    {
        $table = sprintf('#TABLE_PREFIX#%s', $table);
        $this->assertLastQueryEquals(
            sprintf(
                "select `%1\$s`.* from `%1\$s` where `%1\$s`.`%2\$s` = %3\$s and `%1\$s`.`%2\$s` is not null limit 1",
                $table,
                $pkColum,
                $pkValue
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
