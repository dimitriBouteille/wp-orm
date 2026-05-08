<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Models;

use Dbout\WpOrm\Models\Article;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class ArticleTest extends TestCase
{
    /**
     * @return void
     * @covers Article::all
     */
    public function testAll(): void
    {
        $totalObject = 5;
        $expectedType = 'post';
        $objectIds = self::factory()->post->create_many($totalObject, [
            'post_type' => $expectedType,
        ]);

        self::factory()->post->create_many(10, [
            'post_type' => 'order',
        ]);

        $objects = Article::all();
        $this->assertCount($totalObject, $objects->toArray());
        $this->assertEquals($objectIds, $objects->pluck('ID')->toArray());
        $this->assertEquals($expectedType, $objects->first()->getPostType());
    }

    /**
     * @return void
     * @covers Article::save
     * @covers Article::setPostTitle
     * @covers Article::setPostContent
     * @covers Article::setPostExcerpt
     * @covers Article::setPostName
     * @covers Article::setPostStatus
     * @covers Article::getId
     * @covers Article::getPostTitle
     * @covers Article::getPostName
     * @covers Article::getPostExcerpt
     * @covers Article::getPostStatus
     * @covers Article::getPostType
     */
    public function testSave(): void
    {
        $post = new Article();
        $post->setPostTitle("The article title");
        $post->setPostContent("My name is bob.");
        $post->setPostExcerpt("the article excerpt");
        $post->setPostStatus('publish');
        $post->setPostName("demo-123");

        $this->assertTrue($post->save());

        $loadedObject = Article::find($post->getId());
        $this->assertInstanceOf(Article::class, $loadedObject);
        $this->assertEquals('post', $loadedObject->getPostType());
        $this->assertEquals("The article title", $loadedObject->getPostTitle());
        $this->assertEquals("My name is bob.", $loadedObject->getPostContent());
        $this->assertEquals("demo-123", $loadedObject->getPostName());
        $this->assertEquals("the article excerpt", $loadedObject->getPostExcerpt());
        $this->assertEquals("publish", $loadedObject->getPostStatus());
    }
}
