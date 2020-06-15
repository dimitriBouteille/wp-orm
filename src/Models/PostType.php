<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Builders\PostTypeBuilder;
use Dbout\WpOrm\Contracts\PostInterface;
use Illuminate\Events\Dispatcher;
use Udps\Session\Models\Builders\PlaceBuilder;

/**
 * Class PostType
 * @package Dbout\WpOrm\Models
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
abstract class PostType extends Post
{

    /**
     * Post type slug
     * @var string
     */
    protected $_postType;

    /**
     * PostType constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setAttribute(self::POST_TYPE, $this->getPostType());
    }

    /**
     * @return string|null
     */
    public final function getPostType(): ?string
    {
        return $this->_postType;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Dbout\WpOrm\Builders\PostBuilder|PostTypeBuilder|\Dbout\WpOrm\Orm\AbstractModel|\Illuminate\Database\Eloquent\Builder
     */
    public function newEloquentBuilder($query)
    {
        return new PostTypeBuilder($query);
    }

    /**
     * Returns post type slug
     *
     * @return string|null
     */
    public static function postType(): ?string
    {
        return (new static())->getPostType();
    }
}
