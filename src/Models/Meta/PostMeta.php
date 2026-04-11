<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Models\Meta;

use Dbout\WpOrm\Models\Post;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * @return BelongsTo
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, self::POST_ID, Post::POST_ID);
    }
}
