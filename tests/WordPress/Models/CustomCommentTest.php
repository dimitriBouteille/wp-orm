<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
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
        $this->assertLastQueryEquals(sprintf(
            "select `#TABLE_PREFIX#comments`.* from `#TABLE_PREFIX#comments` where `#TABLE_PREFIX#comments`.`comment_ID` = %s and `comment_type` = 'woocommerce' limit 1",
            $objectId
        ));
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

        $this->assertLastQueryEquals(sprintf(
            "select `#TABLE_PREFIX#comments`.* from `#TABLE_PREFIX#comments` where `#TABLE_PREFIX#comments`.`comment_ID` = %s and `comment_type` = 'author' limit 1",
            $objectId
        ));
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

        $comment = new $model();
        $comment->setCommentAuthor('Norman FOSTER');
        $comment->setCommentAuthorEmail('test@test.com');
        $comment->setCommentContent('Hello world');

        $this->assertTrue($comment->save());
        $this->assertEquals('Hello world', $comment->getCommentContent());
        $this->assertEquals('Norman FOSTER', $comment->getCommentAuthor());
        $this->assertEquals('test@test.com', $comment->getCommentAuthorEmail());
        $this->assertEquals('author', $comment->getCommentType());

        $this->assertCommentEqualsToWpComment($comment);
    }

    /**
     * @return void
     * @covers CustomComment::all
     */
    public function testAll(): void
    {
        $applicationCommentsV1 = self::factory()->comment->create_many(5, [
            'comment_type' => 'application',
        ]);

        self::factory()->comment->create_many(10, [
            'comment_type' => 'fake-type',
        ]);

        $applicationCommentsV2 = self::factory()->comment->create_many(7, [
            'comment_type' => 'application',
        ]);

        $model = new class () extends CustomComment {
            protected string $_type = 'application';
        };

        $comments = $model::all();

        $this->assertLastQueryEquals(
            "select `#TABLE_PREFIX#comments`.* from `#TABLE_PREFIX#comments` where `comment_type` = 'application'"
        );

        $applicationComments = array_merge($applicationCommentsV1, $applicationCommentsV2);
        $this->assertEquals(12, $comments->count());
        $this->assertEquals($applicationComments, $comments->pluck('comment_ID')->toArray());
    }

    /**
     * @return void
     * @covers CustomComment::update
     */
    public function testUpdate(): void
    {
        $model = new class () extends CustomComment {
            protected string $_type = 'seo';
        };

        $comment = new $model([
            'comment_author' => 'Zaha HADID',
            'comment_author_email' => 'test@test.com',
            'comment_content' => 'My name is Zaha',
        ]);

        $this->assertTrue($comment->save());
        $comment->setCommentAuthor('Jean Nouvel');
        $comment->setCommentAuthorEmail('contact@jean-nouvel.fr');
        $this->assertEquals('seo', $comment->getCommentType());

        $this->assertTrue($comment->save());
        $this->assertEquals('My name is Zaha', $comment->getCommentContent());
        $this->assertEquals('Jean Nouvel', $comment->getCommentAuthor());
        $this->assertEquals('contact@jean-nouvel.fr', $comment->getCommentAuthorEmail());

        $this->assertCommentEqualsToWpComment($comment);
    }

    /**
     * @return void
     * @covers CustomComment::delete
     */
    public function testDelete(): void
    {
        $model = new class () extends CustomComment {
            protected string $_type = 'seo';
        };

        $comment = new $model([
            'comment_author' => 'Zaha HADID',
            'comment_author_email' => 'test@test.com',
            'comment_content' => 'My name is Zaha',
        ]);

        $comment->save();
        $commentId = $comment->getId();
        $this->assertTrue($comment->delete());
        $this->assertLastQueryEquals(sprintf(
            "delete from `#TABLE_PREFIX#comments` where `comment_ID` = %s",
            $commentId
        ));

        $wpComment = get_comment($commentId);
        $this->assertNull($wpComment);
    }
}
