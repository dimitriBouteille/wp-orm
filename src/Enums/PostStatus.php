<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Dbout\WpOrm\Enums;

/**
 * @see https://wordpress.org/documentation/article/post-status/
 */
enum PostStatus: string
{
    case Publish = 'publish';
    case Future = 'future';
    case Draft = 'draft';
    case Pending = 'pending';
    case Private = 'private';
    case Trash = 'trash';
    case AutoDraft = 'auto-draft';
    case Inherit = 'inherit';
}
