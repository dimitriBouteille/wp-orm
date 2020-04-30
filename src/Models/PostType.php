<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Models\Observes\PostTypeObserves;
use Illuminate\Events\Dispatcher;

/**
 * Class PostType
 * @package Dbout\WpOrm\Models
 *
 * @method static string|null postType();
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
abstract class PostType extends Post
{

    /**
     * @var string|null
     */
    protected $postType = null;

    /**
     * @return string|null
     */
    public function getPostType(): ?string
    {
        return $this->postType;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|void
     */
    public function newQuery()
    {
        $query = parent::newQuery();

        // Set post type
        $query->where(self::POST_TYPE, $this->postType);

        return $query;
    }

    /**
     * @return string|null
     */
    public function scopePostType(): ?string
    {
        return $this->postType;
    }

    /**
     * Add events
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::setEventDispatcher(new Dispatcher());
        static::observe(new PostTypeObserves());
    }

}