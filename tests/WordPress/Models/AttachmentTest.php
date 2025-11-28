<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Models;

use Dbout\WpOrm\Models\Attachment;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class AttachmentTest extends TestCase
{
    /**
     * @return void
     * @covers Attachment::all
     */
    public function testAll(): void
    {
        $totalObject = 5;
        $expectedType = 'attachment';
        $objectIds = self::factory()->post->create_many($totalObject, [
            'post_type' => $expectedType,
        ]);

        self::factory()->post->create_many(10, [
            'post_type' => 'order',
        ]);

        $objects = Attachment::all();
        $this->assertCount($totalObject, $objects->toArray());
        $this->assertEquals($objectIds, $objects->pluck('ID')->toArray());
        $this->assertEquals($expectedType, $objects->first()->getPostType());
    }

    /**
     * @return void
     * @covers Attachment::save
     * @covers Attachment::setPostTitle
     * @covers Attachment::setPostMimeType
     * @covers Attachment::setPostExcerpt
     * @covers Attachment::setPostName
     * @covers Attachment::setPostStatus
     * @covers Attachment::getId
     * @covers Attachment::getPostTitle
     * @covers Attachment::getPostName
     * @covers Attachment::getPostMimeType
     * @covers Attachment::getPostStatus
     * @covers Attachment::getPostType
     */
    public function testSave(): void
    {
        $post = new Attachment();
        $post->setPostTitle("The trip movie");
        $post->setPostExcerpt("The Tokyo trip movie");
        $post->setPostStatus('publish');
        $post->setPostName("the-tokyo-trip");
        $post->setPostMimeType("video/mp4");

        $this->assertTrue($post->save());

        $loadedObject = Attachment::find($post->getId());
        $this->assertInstanceOf(Attachment::class, $loadedObject);
        $this->assertEquals('attachment', $loadedObject->getPostType());
        $this->assertEquals("The trip movie", $loadedObject->getPostTitle());
        $this->assertEquals("publish", $loadedObject->getPostStatus());
        $this->assertEquals("the-tokyo-trip", $loadedObject->getPostName());
        $this->assertEquals("video/mp4", $loadedObject->getPostMimeType());
    }
}
