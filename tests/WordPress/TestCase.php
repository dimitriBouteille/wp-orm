<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress;

use Dbout\WpOrm\Models\Post;

abstract class TestCase extends \WP_UnitTestCase
{
    /**
     * @param string $query
     * @return void
     */
    public static function assertLastQueryEqual(string $query): void
    {
        global $wpdb;
        self::assertEquals($query, $wpdb->last_query);
    }

    /**
     * @param Post|null $model
     * @param \WP_Post|null $wpPost
     * @return void
     */
    public static function assertPostEqualToWpObject(?Post $model, ?\WP_Post $wpPost): void
    {
        self::assertInstanceOf(\WP_Post::class, $wpPost);

        self::assertEquals($wpPost->ID, $model->getId());
        self::assertEquals($wpPost->post_type, $model->getPostType());
        self::assertEquals($wpPost->post_content, $model->getPostContent());
        self::assertEquals($wpPost->post_title, $model->getPostTitle());
        self::assertEquals($wpPost->post_name, $model->getPostName());
    }

    /**
     * @param string $columnName
     * @return void
     */
    public static function expectExceptionUnknownColumn(string $columnName): void
    {
        self::expectExceptionMessageMatches(sprintf("/^Unknown column '%s' in 'field list'/", $columnName));
    }

    /**
     * @param string $table
     * @return string
     */
    protected function getTable(string $table): string
    {
        global $wpdb;
        return $wpdb->prefix . $table;
    }
}
