<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Contracts\PostInterface;
use Dbout\WpOrm\Contracts\PostMetaInterface;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class PostMeta
 * @package Dbout\WpOrm\Models
 *
 * @method static PostMetaInterface find(int $metaId);
 * @property PostInterface|null $post
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
class PostMeta extends AbstractMeta implements PostMetaInterface
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
     * @return HasOne
     */
    public function post(): HasOne
    {
        return $this->hasOne(Post::class, PostInterface::POST_ID, self::META_ID);
    }

}