<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Taps\Post;

use Dbout\WpOrm\Builders\PostBuilder;
use Dbout\WpOrm\Enums\PingStatus;
use Dbout\WpOrm\Models\Post;

readonly class IsPingStatusTap
{
    /**
     * @param string|PingStatus $status
     */
    public function __construct(
        protected string|PingStatus $status
    ) {
    }

    /**
     * @param PostBuilder $builder
     * @return void
     */
    public function __invoke(PostBuilder $builder): void
    {
        $status = $this->status;
        if ($status instanceof PingStatus) {
            $status = $status->value;
        }

        $builder->where(Post::PING_STATUS, $status);
    }
}
