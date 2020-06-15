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
     * @return Builder[]|\Illuminate\Database\Eloquent\Model[]
     */
    public function getModels($columns = ['*'])
    {
        $postType = $this->model->getPostType();

        return $this->model->hydrate(
            $this->query->where(PostType::POST_TYPE, $postType)->get($columns)->all()
        )->all();
    }
}
