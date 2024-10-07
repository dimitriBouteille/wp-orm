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
            'post_name' => 'product-testFindOneByGuid',
            // Auto add http://
            'guid' => 'guid-testFindOneByGuid',
        ]);

        $post = Post::findOneByGuid('http://guid-testFindOneByGuid');
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
            'post_name' => 'product-testFindOneByName',
            'guid' => 'guid-testFindOneByName',
        ]);

        $post = Post::findOneByGuid('http://guid-testFindOneByName-fake');
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
            'post_name' => 'product-testFindOneByName',
            'guid' => 'guid-testFindOneByName',
        ]);

        $post = Post::findOneByName('product-testFindOneByName');
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
            'post_name' => 'product-testFindOneByNameWithNotFound',
            'guid' => 'guid-testFindOneByNameWithNotFound',
        ]);

        $post = Post::findOneByName('product-testFindOneByNameWithNotFound-fake');
        $this->assertNull($post);
    }

    /**
     * @return void
     * @covers Post::parent
     */
    public function testParent(): void
    {
        $objectId = self::factory()->post->create([
            'post_type' => 'product',
            'post_content'  => 'Child product XX',
            'post_name' => 'child-product',
        ]);

        $object = new Post();
        $object->setPostName('product-test');
        $object->setPostTitle('Product test');
        $object->setPostParent($objectId);

        $this->assertTrue($object->save());
        $this->assertEquals($objectId, $object->getPostParent());

        $newObject = Post::find($object->getId());
        $parent = $newObject->parent;
        $this->assertLastQueryHasOneRelation('posts', 'post_parent', $objectId);

        $this->assertInstanceOf(Post::class, $parent);
        $this->assertEquals($objectId, $parent->getId());
    }
}
