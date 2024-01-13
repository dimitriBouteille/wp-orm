<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Builders;

use Dbout\WpOrm\Api\PostInterface;
use Dbout\WpOrm\Builders\Traits\WithMeta;
use Dbout\WpOrm\Models\Post;
use Illuminate\Database\Eloquent\Collection;

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

        /** @var Post|null $model */
        $model = $this->firstWhere(PostInterface::POST_NAME, $name);
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
        return $this->_whereOrIn(PostInterface::TYPE, $types);
    }

    /**
     * @param $author
     * @return $this
     */
    public function whereAuthor($author): self
    {
        $this->where(PostInterface::AUTHOR, $author);
        return $this;
    }

    /**
     * @param mixed ...$status
     * @return $this
     */
    public function whereStatus(...$status): self
    {
        return $this->_whereOrIn(PostInterface::STATUS, $status);
    }
}
