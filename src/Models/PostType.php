<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Contracts\PostInterface;
use Illuminate\Events\Dispatcher;

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
        $this->setAttribute(self::POST_TYPE, $this->postType);
    }

    /**
     * @return string|null
     */
    public final function getPostType(): ?string
    {
        return $this->_postType;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery()
    {
        // Set post type
        $query = parent::newQuery();
        $query->where(self::POST_TYPE, $this->_postType);

        return $query;
    }

    /**
     * Add events
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::setEventDispatcher(new Dispatcher());
        static::saving(function(PostInterface $model) {
            $model->setPostType($model->getPostType());
        });
    }

}