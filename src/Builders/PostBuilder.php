<?php

namespace Dbout\WpOrm\Builders;

use Dbout\WpOrm\Models\Post;

/**
 * Class PostBuilder
 * @package Dbout\WpOrm\Builders
 */
class PostBuilder extends AbstractBuilder
{

    /**
     * @param mixed ...$types
     * @return $this
     */
    public function types(...$types): self
    {
        return $this->_whereOrIn(Post::POST_TYPE, $types);
    }

    /**
     * @param $author
     * @return $this
     */
    public function author($author): self
    {
        $this->where(Post::POST_AUTHOR, $author);
        return $this;
    }

    /**
     * @param mixed ...$status
     * @return $this
     */
    public function status(...$status): self
    {
        return $this->_whereOrIn(Post::POST_STATUS, $status);
    }
}
