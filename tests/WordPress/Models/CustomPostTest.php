<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
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

        $this->assertLastQueryEquals(sprintf(
            "select `#TABLE_PREFIX#posts`.* from `#TABLE_PREFIX#posts` where `#TABLE_PREFIX#posts`.`ID` = %s and `post_type` = 'architect' limit 1",
            $objectId
        ));
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
        $this->assertLastQueryEquals(sprintf(
            "select `#TABLE_PREFIX#posts`.* from `#TABLE_PREFIX#posts` where `#TABLE_PREFIX#posts`.`ID` = %s and `post_type` = 'architect' limit 1",
            $objectId
        ));
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

    /**
     * @return void
     * @covers CustomPost::all
     */
    public function testAll(): void
    {
        $objectsV1 = self::factory()->post->create_many(5, [
            'post_type' => 'project',
        ]);

        self::factory()->post->create_many(10, [
            'post_type' => 'fake-project',
        ]);

        $objectsV2 = self::factory()->post->create_many(7, [
            'post_type' => 'project',
        ]);

        $model = new class () extends CustomPost {
            protected string $_type = 'project';
        };

        $projects = $model::all();

        $this->assertLastQueryEquals(
            "select `#TABLE_PREFIX#posts`.* from `#TABLE_PREFIX#posts` where `post_type` = 'project'"
        );

        $objectIds = array_merge($objectsV1, $objectsV2);
        $this->assertEquals(12, $projects->count());
        $this->assertEquals($objectIds, $projects->pluck('ID')->toArray());
    }

    /**
     * @return void
     * @covers CustomPost::delete
     */
    public function testDelete(): void
    {
        $model = new class () extends CustomPost {
            protected string $_type = 'order';
        };

        $order = new $model();
        $order->setPostName('order-15');
        $order->setPostTitle('Order #15');
        $order->save();

        $objectId = $order->getId();
        $this->assertTrue($order->delete());
        $this->assertLastQueryEquals(sprintf(
            "delete from `#TABLE_PREFIX#posts` where `ID` = %s",
            $objectId
        ));

        $wpObject = get_post($objectId);
        $this->assertNull($wpObject);
    }
}
