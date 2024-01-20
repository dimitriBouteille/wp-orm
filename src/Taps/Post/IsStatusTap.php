<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Taps\Post;

use Dbout\WpOrm\Builders\PostBuilder;
use Dbout\WpOrm\Enums\PostStatus;
use Dbout\WpOrm\Models\Post;

/**
 * @since 3.0.0
 */
class IsStatusTap
{
    /**
     * @param string|PostStatus $status
     */
    public function __construct(
        protected readonly string|PostStatus $status
    ) {
    }

    /**
     * @param PostBuilder $builder
     * @return void
     */
    public function __invoke(PostBuilder $builder): void
    {
        $status = $this->status;
        if ($status instanceof PostStatus) {
            $status = $status->value;
        }

        $builder->where(Post::STATUS, $status);
    }
}
