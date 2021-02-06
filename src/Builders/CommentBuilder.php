<?php

namespace Dbout\WpOrm\Builders;

use Dbout\WpOrm\Models\Comment;

/**
 * Class CommentBuilder
 * @package Dbout\WpOrm\Builders
 */
class CommentBuilder extends AbstractBuilder
{

    /**
     * @param mixed ...$types
     * @return $this
     */
    public function types(...$types): self
    {
        return $this->_whereOrIn(Comment::TYPE, $types);
    }
}