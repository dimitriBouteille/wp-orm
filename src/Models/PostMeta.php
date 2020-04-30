<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Contracts\PostInterface;
use Dbout\WpOrm\Contracts\PostMetaInterface;
use Dbout\WpOrm\Orm\AbstractModel;

/**
 * Class PostMeta
 * @package Dbout\WpOrm\Models
 *
 * @method static PostMetaInterface find(int $metaId);
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
class PostMeta extends AbstractModel implements PostMetaInterface
{

    /**
     * @var string
     */
    protected $table = 'postmeta';

    /**
     * @var string
     */
    protected $primaryKey = self::META_ID;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|PostInterface|mixed
     */
    public function getPost()
    {
        return $this->hasOne(Post::class, PostInterface::POST_ID, self::POST_ID);
    }

    /**
     * @param $post
     * @return PostMetaInterface
     */
    public function setPost($post): PostMetaInterface
    {
        if($post instanceof \WP_Post) {
            $post = $post->ID;
        } else if($post instanceof PostInterface) {
            $post = $post->getId();
        }

        $this->setAttribute(self::POST_ID, $post);
        return $this;
    }

    /**
     * @param string $key
     * @return PostMetaInterface
     */
    public function setMetaKey(string $key): PostMetaInterface
    {
        $this->setAttribute(self::META_KEY, $key);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMetaKey(): ?string
    {
        return $this->getAttribute(self::META_KEY);
    }

    /**
     * @param $value
     * @return PostMetaInterface
     */
    public function setMetaValue($value): PostMetaInterface
    {
        $this->setAttribute(self::META_VALUE, $value);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMetaValue()
    {
        return $this->getAttribute(self::META_VALUE);
    }

}