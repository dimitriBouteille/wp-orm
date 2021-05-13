<?php

namespace Dbout\WpOrm\Builders;

use Dbout\WpOrm\Models\Comment;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class CommentBuilder
 * @package Dbout\WpOrm\Builders
 */
class CommentBuilder extends AbstractBuilder
{

    /**
     * @param string $type
     * @return Collection
     */
    public function findAllByType(string $type): Collection
    {
        return $this
            ->whereTypes([$type])
            ->get();
    }

    /**
     * @param mixed ...$types
     * @return $this
     */
    public function whereTypes(...$types): self
    {
        return $this->_whereOrIn(Comment::TYPE, $types);
    }
}