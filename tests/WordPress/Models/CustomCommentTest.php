<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Models;

use Dbout\WpOrm\Models\CustomComment;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class CustomCommentTest extends TestCase
{
    /**
     * @return void
     * @covers CustomComment::find
     */
    public function testFindValidType(): void
    {
        $objectId = self::factory()->comment->create([
            'comment_type' => 'woocommerce',
        ]);

        $model = new class () extends CustomComment {
            protected string $_type = 'woocommerce';
        };

        $object = $model::find($objectId);
        $this->assertInstanceOf($model::class, $object);
        $this->assertEquals('woocommerce', $object->getCommentType());
        $this->assertEquals($objectId, $object->getId());
    }

    /**
     * @return void
     * @covers CustomComment::find
     */
    public function testFindWithDifferentType(): void
    {
        $objectId = self::factory()->comment->create([
            'comment_type' => 'woocommerce',
        ]);

        $model = new class () extends CustomComment {
            protected string $_type = 'author';
        };

        $object = $model::find($objectId);
        $this->assertNull($object);
    }

    /**
     * @return void
     * @covers CustomComment::save
     */
    public function testSave(): void
    {
        $model = new class () extends CustomComment {
            protected string $_type = 'author';
        };

        $comment = new $model([
            'comment_author' => 'Norman FOSTER',
            'comment_author_email' => 'test@test.com',
            'comment_content' => 'Hello world',
        ]);

        $this->assertTrue($comment->save());
        $this->assertEquals('Hello world', $comment->getCommentContent());
        $this->assertEquals('Norman FOSTER', $comment->getCommentAuthor());
        $this->assertEquals('test@test.com', $comment->getCommentAuthorEmail());
        $this->assertEquals('author', $comment->getCommentType());

        $wpComment = get_comment($comment->getId());
        $this->assertEquals($wpComment->comment_ID, $comment->getId());
        $this->assertEquals($wpComment->comment_content, $comment->getCommentContent());
        $this->assertEquals($wpComment->comment_author_email, $comment->getCommentAuthorEmail());
        $this->assertEquals($wpComment->comment_author, $comment->getCommentAuthor());
        $this->assertEquals($wpComment->comment_type, $comment->getCommentType());
    }

    /**
     * @return void
     * @covers CustomComment::all
     */
    public function testQueryAll(): void
    {
        $applicationComments = self::factory()->comment->create_many(5, [
            'comment_type' => 'application',
        ]);

        $badComments = self::factory()->comment->create_many(10, [
            'comment_type' => 'fake-type',
        ]);

        $model = new class () extends CustomComment {
            protected string $_type = 'application';
        };

        $comments = $model::all();
        $this->assertEquals(5, $comments->count());
        $this->assertEquals($applicationComments, $comments->pluck('comment_ID')->toArray());
    }
}
