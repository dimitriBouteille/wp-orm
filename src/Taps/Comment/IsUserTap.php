<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Taps\Comment;

use Dbout\WpOrm\Builders\CommentBuilder;
use Dbout\WpOrm\Models\Comment;
use Dbout\WpOrm\Models\User;

/**
 * @since 3.0.0
 */
class IsUserTap
{
    /**
     * @param int|User $user
     */
    public function __construct(
        protected readonly int|User $user
    ) {
    }

    /**
     * @param CommentBuilder $builder
     * @return void
     */
    public function __invoke(CommentBuilder $builder): void
    {
        $user = $this->user;
        if ($user instanceof User) {
            $user = $user->getId();
        }

        $builder->where(Comment::USER_ID, $user);
    }
}
