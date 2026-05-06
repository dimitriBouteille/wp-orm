<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Support;

use Dbout\WpOrm\Models\Post;

/**
 * Test fixture helpers to reduce the new-Post-set-title-set-name-set-type-save
 * boilerplate that recurs across builder, meta and concern tests.
 *
 * @see TESTS_AUDIT.md point #5 (data providers + BuildsTestPost trait)
 */
trait BuildsTestPost
{
    /**
     * Create and save a Post with sensible defaults.
     *
     * @param string $title
     * @param string $type
     * @return Post
     */
    protected function aPost(string $title = 'Test post', string $type = 'post'): Post
    {
        $post = new Post();
        $post->setPostTitle($title);
        $post->setPostName(sanitize_title($title));
        $post->setPostType($type);
        $post->save();

        return $post;
    }

    /**
     * Create and save a Post, then attach the given metas one by one (after
     * save, so no event dispatcher is required).
     *
     * @param array<string, mixed> $metas
     * @param string $title
     * @param string $type
     * @return Post
     */
    protected function aPostWithMetas(
        array $metas,
        string $title = 'Test post with metas',
        string $type = 'post'
    ): Post {
        $post = $this->aPost($title, $type);
        foreach ($metas as $key => $value) {
            $post->setMeta($key, $value);
        }

        return $post;
    }

    /**
     * Create and save a Post-derived anonymous class with custom $metaCasts,
     * then attach the given metas. Used by HasMetas cast tests where the
     * cast configuration must be set at class level.
     *
     * @param array<string, string> $casts Map of meta key → cast type.
     * @param array<string, mixed>  $metas Optional metas to set after save.
     * @param string                $title
     * @return Post
     */
    protected function aPostWithMetaCasts(
        array $casts,
        array $metas = [],
        string $title = 'Test cast post'
    ): Post {
        $post = new class () extends Post {
            /**
             * @var array<string, string>
             */
            protected array $metaCasts = [];

            /**
             * @param array<string, string> $casts
             * @return self
             */
            public function withMetaCasts(array $casts): self
            {
                $this->metaCasts = $casts;
                return $this;
            }
        };

        $post->withMetaCasts($casts);
        $post->setPostTitle($title);
        $post->setPostName(sanitize_title($title));
        $post->setPostType('post');
        $post->save();

        foreach ($metas as $key => $value) {
            $post->setMeta($key, $value);
        }

        return $post;
    }
}
