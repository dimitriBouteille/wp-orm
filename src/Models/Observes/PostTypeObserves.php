<?php

namespace Dbout\WpOrm\Models\Observes;

use Dbout\WpOrm\Models\Contracts\PostInterface;

/**
 * Class PostObserves
 * @package Dbout\WpOrm\Models\Observes
 */
class PostTypeObserves
{

    /**
     * @param PostInterface $model
     */
    public function saving(PostInterface $model)
    {
        $postType = $model->getPostType();
        if($postType) {
            $model->setPostType($postType);
        }
    }

}