<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
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
}
