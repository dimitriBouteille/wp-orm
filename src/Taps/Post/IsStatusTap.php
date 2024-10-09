<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Taps\Post;

use Dbout\WpOrm\Builders\PostBuilder;
use Dbout\WpOrm\Enums\PostStatus;
use Dbout\WpOrm\Models\Post;

readonly class IsStatusTap
{
    /**
     * @param string|PostStatus $status
     */
    public function __construct(
        protected string|PostStatus $status
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
