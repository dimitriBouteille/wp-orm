<?php

namespace Dbout\WpOrm\Observers;

use Dbout\WpOrm\Models\PostType;

/**
 * Class PostTypeObserver
 * @package Dbout\WpOrm\Observers
 */
class PostTypeObserver
{

    /**
     * @param PostType $postType
     */
    public function saving(PostType $postType)
    {
        $postType->setPostType($postType->getPostType());
    }
}
