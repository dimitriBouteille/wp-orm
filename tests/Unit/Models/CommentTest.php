<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\Unit\Models;

use Dbout\WpOrm\Models\Comment;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

#[CoversClass(Comment::class)]
#[CoversFunction('getCommentAuthorIP')]
#[CoversFunction('setCommentAuthorIP')]
#[CoversFunction('getCommentPostID')]
#[CoversFunction('setCommentPostID')]
class CommentTest extends TestCase
{
    /**
     * @return void
     */
    public function testCommentAuthorIP(): void
    {
        $comment = new Comment();
        $comment->setCommentAuthorIP('127.0.0.1');
        $this->assertEquals('127.0.0.1', $comment->getCommentAuthorIP());
        $this->assertEquals('127.0.0.1', $comment->getAttribute('comment_author_IP'));
    }

    /**
     * @return void
     */
    public function testCommentPostID(): void
    {
        $comment = new Comment();
        $comment->setCommentPostID(1525);
        $this->assertEquals(1525, $comment->getCommentPostID());
        $this->assertEquals(1525, $comment->getAttribute('comment_post_ID'));
    }
}
