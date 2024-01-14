<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Taps\Post;

use Dbout\WpOrm\Api\PostInterface;
use Dbout\WpOrm\Builders\PostBuilder;
use Dbout\WpOrm\Enums\PingStatus;

/**
 * @since 3.0.0
 */
class IsPingStatusTap
{
    /**
     * @param string|PingStatus $status
     */
    public function __construct(
        protected readonly string|PingStatus $status
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

        $builder->where(PostInterface::PING_STATUS, $status);
    }
}
