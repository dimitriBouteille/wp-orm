<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Models;

use Dbout\WpOrm\Models\Comment;
use Dbout\WpOrm\Models\Post;
use Dbout\WpOrm\Models\User;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class CommentTest extends TestCase
{
    /**
     * @return void
     * @covers Comment::user
     */
    public function testUser(): void
    {
        $userId = self::factory()->user->create([
            'user_login' => 'test-15',
            'user_pass'  => 'testing',
            'user_email' => 'test-15@test.com',
        ]);

        $comment = new Comment();
        $comment->setUserId($userId);
        $comment->setCommentContent('Hello world');
        $this->assertTrue($comment->save());
        $this->assertEquals('Hello world', $comment->getCommentContent());
        $this->assertEquals($userId, $comment->getUserId());

        $reloadComment = Comment::find($comment->getId());
        $user = $reloadComment->user;
        $this->assertLastQueryHasOneRelation('users', 'ID', $userId);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userId, $user->getId());
    }

    /**
     * @return void
     * @covers Comment::post
     */
    public function testPost(): void
    {
        $postId = self::factory()->post->create([
            'post_type' => 'product',
            'post_content'  => 'product information',
            'post_name' => 'product-15-10-15',
        ]);

        $comment = new Comment();
        $comment->setCommentPostID($postId);
        $comment->setCommentType('woocommerce');
        $comment->setCommentContent('custom notification');

        $this->assertTrue($comment->save());
        $this->assertEquals($postId, $comment->getCommentPostID());

        $reloadComment = Comment::find($comment->getId());
        $post = $reloadComment->post;
        $this->assertLastQueryHasOneRelation('posts', 'ID', $postId);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals($postId, $post->getId());
    }

    /**
     * @return void
     * @covers Comment::parent
     */
    public function testParent(): void
    {
        $objectId = self::factory()->comment->create([
            'comment_author_email' => 'test@test.fr',
            'comment_content'  => 'Comment content',
        ]);

        $comment = new Comment();
        $comment->setCommentParent($objectId);
        $comment->setCommentType('woocommerce');
        $comment->setCommentContent('custom notification');

        $this->assertTrue($comment->save());
        $this->assertEquals($objectId, $comment->getCommentParent());

        $reloadComment = Comment::find($comment->getId());
        $parent = $reloadComment->parent;
        $this->assertLastQueryHasOneRelation('comments', 'comment_ID', $objectId);

        $this->assertInstanceOf(Comment::class, $parent);
        $this->assertEquals($objectId, $parent->getId());
    }

    /**
     * @return void
     * @covers Comment::save
     */
    public function testSave(): void
    {
        $comment = new Comment();
        $comment->setCommentContent('I think the piano on Sunset Jesus is a masterpiece.');
        $comment->setCommentAuthor('15');
        $comment->setCommentAuthorIP('127.0.0.1');
        $comment->setCommentAgent('chrome');
        $comment->setCommentAuthorEmail('test@test.com');
        $comment->setCommentPostID(165);
        $comment->setCommentType('custom');
        $comment->setCommentApproved('yes');
        $comment->setCommentAuthorUrl('https://my-site.com');
        $comment->setCommentParent(6525);

        $this->assertTrue($comment->save());

        $loadComment = Comment::find($comment->getId());
        $this->assertInstanceOf(Comment::class, $loadComment);
        $this->assertEquals('I think the piano on Sunset Jesus is a masterpiece.', $comment->getCommentContent());
        $this->assertEquals('15', $comment->getCommentAuthor());
        $this->assertEquals('127.0.0.1', $comment->getCommentAuthorIP());
        $this->assertEquals('chrome', $comment->getCommentAgent());
        $this->assertEquals('test@test.com', $comment->getCommentAuthorEmail());
        $this->assertEquals(165, $comment->getCommentPostID());
        $this->assertEquals('custom', $comment->getCommentType());
        $this->assertEquals('yes', $comment->getCommentApproved());
        $this->assertEquals('https://my-site.com', $comment->getCommentAuthorUrl());
        $this->assertEquals(6525, $comment->getCommentParent());
    }
}
