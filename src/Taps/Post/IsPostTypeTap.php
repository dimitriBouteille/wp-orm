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

/**
 * @since 3.0.0
 */
class IsPostTypeTap
{
    /**
     * @param string $postType
     */
    public function __construct(
        protected readonly string $postType
    ) {
    }

    /**
     * @param PostBuilder $builder
     * @return void
     */
    public function __invoke(PostBuilder $builder): void
    {
        $builder->where(Post::TYPE, $this->postType);
    }
}
