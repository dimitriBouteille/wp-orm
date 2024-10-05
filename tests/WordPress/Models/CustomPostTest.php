<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Models;

use Dbout\WpOrm\Models\CustomPost;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class CustomPostTest extends TestCase
{
    /**
     * @return void
     * @covers CustomPost::find
     */
    public function testFindWithValidPostType(): void
    {
        $objectId = self::factory()->post->create([
            'post_type' => 'architect',
        ]);

        $model = new class () extends CustomPost {
            protected string $_type = 'architect';
        };

        $object = $model::find($objectId);
        $this->assertInstanceOf($model::class, $object);
        $this->assertEquals('architect', $object->getPostType());
        $this->assertEquals($objectId, $object->getId());
    }

    /**
     * @return void
     * @covers CustomPost::find
     */
    public function testFindWithDifferentType(): void
    {
        $objectId = self::factory()->post->create([
            'post_type' => 'product',
        ]);

        $model = new class () extends CustomPost {
            protected string $_type = 'architect';
        };

        $object = $model::find($objectId);
        $this->assertNull($object, 'Value must be null because cannot load another post_type object.');
    }

    /**
     * @return void
     * @covers CustomPost::save
     */
    public function testSave(): void
    {
        $model = new class () extends CustomPost {
            protected string $_type = 'architect';
        };

        $architect = new $model();
        $architect->setPostTitle('Norman FOSTER');
        $architect->setPostContent('Content - My name is Norman FOSTER');
        $architect->setPostExcerpt('Excerpt - My name is Norman FOSTER');
        $architect->setPostStatus('publish');
        $architect->setPostName('norman-foster');

        $this->assertTrue($architect->save());
        $this->assertEquals('Norman FOSTER', $architect->getPostTitle());
        $this->assertEquals('Content - My name is Norman FOSTER', $architect->getPostContent());
        $this->assertEquals('Excerpt - My name is Norman FOSTER', $architect->getPostExcerpt());
        $this->assertEquals('publish', $architect->getPostStatus());
        $this->assertEquals('norman-foster', $architect->getPostName());
        $this->assertEquals('architect', $architect->getPostType());

        $this->assertPostEqualsToWpPost($architect);
    }
}
