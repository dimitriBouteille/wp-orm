<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models\Meta;

use Dbout\WpOrm\Models\Post;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read Post|null $post
 */
class PostMeta extends AbstractMeta
{
    final public const META_ID = 'meta_id';
    final public const POST_ID = 'post_id';

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
        return $this->hasOne(Post::class, Post::POST_ID, self::POST_ID);
    }
}
