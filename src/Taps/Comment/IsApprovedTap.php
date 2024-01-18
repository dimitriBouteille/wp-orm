<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Taps\Comment;

use Dbout\WpOrm\Api\CommentInterface;
use Dbout\WpOrm\Builders\CommentBuilder;

/**
 * @since 3.0.0
 */
class IsApprovedTap
{
    /**
     * @param bool $isApproved
     */
    public function __construct(
        protected readonly bool $isApproved = true
    ) {
    }

    /**
     * @param CommentBuilder $builder
     * @return void
     */
    public function __invoke(CommentBuilder $builder): void
    {
        $builder->where(CommentInterface::APPROVED, $this->isApproved);
    }
}