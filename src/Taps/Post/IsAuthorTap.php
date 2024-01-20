<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Taps\Post;

use Dbout\WpOrm\Builders\PostBuilder;
use Dbout\WpOrm\Models\Post;
use Dbout\WpOrm\Models\User;

/**
 * @since 3.0.0
 */
class IsAuthorTap
{
    /**
     * @param int|User $author
     */
    public function __construct(
        protected int|User $author
    ) {
    }

    /**
     * @param PostBuilder $builder
     * @return void
     */
    public function __invoke(PostBuilder $builder): void
    {
        $author = $this->author;
        if ($author instanceof User) {
            $author = $author->getId();
        }

        $builder->where(Post::AUTHOR, $author);
    }
}
