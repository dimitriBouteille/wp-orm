<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Models;

use Dbout\WpOrm\Models\Page;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class PageTest extends TestCase
{
    /**
     * @return void
     * @covers Page::all
     */
    public function testAll(): void
    {
        $totalObject = 5;
        $expectedType = 'page';
        $objectIds = self::factory()->post->create_many($totalObject, [
            'post_type' => $expectedType,
        ]);

        self::factory()->post->create_many(10, [
            'post_type' => 'order',
        ]);

        $objects = Page::all();
        $this->assertCount($totalObject, $objects->toArray());
        $this->assertEquals($objectIds, $objects->pluck('ID')->toArray());
        $this->assertEquals($expectedType, $objects->first()->getPostType());
    }

    /**
     * @return void
     * @covers Page::save
     * @covers Page::setPostTitle
     * @covers Page::setPostExcerpt
     * @covers Page::setPostName
     * @covers Page::setPostStatus
     * @covers Page::getId
     * @covers Page::getPostTitle
     * @covers Page::getPostName
     * @covers Page::getPostStatus
     * @covers Page::getPostType
     */
    public function testSave(): void
    {
        $post = new Page();
        $post->setPostTitle("Where is Paris ?");
        $post->setPostExcerpt("Find Paris in the world");
        $post->setPostStatus('closed');
        $post->setPostName("where-is-paris");

        $this->assertTrue($post->save());

        $loadedObject = Page::find($post->getId());
        $this->assertInstanceOf(Page::class, $loadedObject);
        $this->assertEquals('page', $loadedObject->getPostType());
        $this->assertEquals("Where is Paris ?", $loadedObject->getPostTitle());
        $this->assertEquals("Find Paris in the world", $loadedObject->getPostExcerpt());
        $this->assertEquals("closed", $loadedObject->getPostStatus());
        $this->assertEquals("where-is-paris", $loadedObject->getPostName());
    }
}
