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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversFunction;

#[CoversClass(CustomComment::class)]
#[CoversFunction('find')]
#[CoversFunction('save')]
#[CoversFunction('all')]
#[CoversFunction('update')]
class CustomCommentTest extends TestCase
{
    /**
     * @return void
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
     */
    public function testQueryAll(): void
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
        $applicationComments = array_merge($applicationCommentsV1, $applicationCommentsV2);
        $this->assertEquals(12, $comments->count());
        $this->assertEquals($applicationComments, $comments->pluck('comment_ID')->toArray());
    }

    /**
     * @return void
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
}
