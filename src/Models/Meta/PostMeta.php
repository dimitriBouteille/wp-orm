<?php

namespace Dbout\WpOrm\Models\Meta;

use Dbout\WpOrm\Models\Post;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class PostMeta
 * @package Dbout\WpOrm\Models\Meta
 *
 * @method static PostMeta find(int $metaId);
 * @property Post|null $post
 */
class PostMeta extends AbstractMeta
{

    const META_ID = 'meta_id';
    const POST_ID = 'post_id';

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
