<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Builders;

use Dbout\WpOrm\Models\Post;
use Illuminate\Database\Eloquent\Collection;

class PostBuilder extends AbstractWithMetaBuilder
{
    /**
     * @param string|null $name
     * @return Post|null
     * @deprecated Remove in next version
     * @see Post::findOneByName()
     */
    public function findOneByName(?string $name): ?Post
    {
        if (!$name) {
            return null;
        }

        /** @var Post|null $model */
        $model = $this->firstWhere(Post::POST_NAME, $name);
        return $model;
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
