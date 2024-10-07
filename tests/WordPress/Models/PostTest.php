<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Models;

use Dbout\WpOrm\Models\Post;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class PostTest extends TestCase
{
    /**
     * @return void
     * @covers Post::findOneByGuid
     */
    public function testFindOneByGuid(): void
    {
        self::factory()->post->create([
            'post_type' => 'product',
            'post_content'  => 'product information',
            'post_name' => 'product-1585656',
            'guid' => 'guid-1585656',
        ]);

        $post = Post::findOneByGuid('guid-1585656');

        global $wpdb;
        var_dump($wpdb->last_query);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertPostEqualsToWpPost($post);
    }

    /**
     * @return void
     * @covers Post::findOneByGuid
     */
    public function testFindOneByGuidWithNotFound(): void
    {
        self::factory()->post->create([
            'post_type' => 'product',
            'post_content'  => 'product information',
            'post_name' => 'product-1585656',
            'guid' => 'guid-1585656',
        ]);

        $post = Post::findOneByGuid('guid-1585656-fake');
        $this->assertNull($post);
    }

    /**
     * @return void
     * @covers Post::findOneByName
     */
    public function testFindOneByName(): void
    {
        self::factory()->post->create([
            'post_type' => 'product',
            'post_content'  => 'product AABB',
            'post_name' => 'product-AABB',
            'guid' => 'guid-AABB',
        ]);

        $post = Post::findOneByName('product-AABB');
        $this->assertInstanceOf(Post::class, $post);
        $this->assertPostEqualsToWpPost($post);
    }

    /**
     * @return void
     * @covers Post::findOneByName
     */
    public function testFindOneByNameWithNotFound(): void
    {
        self::factory()->post->create([
            'post_type' => 'product',
            'post_content'  => 'product AABB',
            'post_name' => 'product-AABB',
            'guid' => 'guid-AABB',
        ]);

        $post = Post::findOneByName('product-AABB-fake');
        $this->assertNull($post);
    }
}
