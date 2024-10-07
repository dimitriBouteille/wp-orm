<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Models;

use Dbout\WpOrm\Models\Comment;
use Dbout\WpOrm\Models\User;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class CommentTest extends TestCase
{
    /**
     * @return void
     * @covers Comment::user()
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

        $reloadComment = Comment::find($comment->getId());
        $user = $reloadComment->user;

        global $wpdb;
        var_dump($wpdb->last_query);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userId, $user->getId());
    }

    /**
     * @return void
     * @covers Comment::post()
     */
    public function testPost(): void
    {

    }

    /**
     * @return void
     * @covers Comment::parent
     */
    public function testParent(): void
    {

    }
}
