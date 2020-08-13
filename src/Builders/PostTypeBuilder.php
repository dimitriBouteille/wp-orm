<?php

namespace Dbout\WpOrm\Builders;

use Dbout\WpOrm\Models\PostType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Class PostTypeBuilder
 * @package Dbout\WpOrm\Builders
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
class PostTypeBuilder extends Builder
{

    /**
     * @var PostType
     */
    protected $model;

    /**
     * @param array $columns
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function get($columns = ['*'])
    {
        $postType = $this->model->getPostType();
        $this->query
            ->where(PostType::POST_TYPE, $postType);

        return parent::get($columns);
    }
}
