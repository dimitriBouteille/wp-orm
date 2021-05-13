<?php

namespace Dbout\WpOrm\Builders;

use Dbout\WpOrm\Builders\Traits\WithMeta;
use Dbout\WpOrm\Models\Post;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class PostBuilder
 * @package Dbout\WpOrm\Builders
 */
class PostBuilder extends AbstractBuilder
{

    use WithMeta;

    /**
     * @param string|null $name
     * @return Post|null
     */
    public function findOneByName(?string $name): ?Post
    {
        if (!$name) {
            return null;
        }

        return $this
            ->where(Post::POST_NAME, $name)
            ->first();
    }

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
        return $this->_whereOrIn(Post::TYPE, $types);
    }

    /**
     * @param $author
     * @return $this
     */
    public function whereAuthor($author): self
    {
        $this->where(Post::AUTHOR, $author);
        return $this;
    }

    /**
     * @param mixed ...$status
     * @return $this
     */
    public function whereStatus(...$status): self
    {
        return $this->_whereOrIn(Post::STATUS, $status);
    }
}
