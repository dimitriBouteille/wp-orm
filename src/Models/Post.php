<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Carbon\Carbon;
use Dbout\WpOrm\Api\WithMetaModelInterface;
use Dbout\WpOrm\Builders\PostBuilder;
use Dbout\WpOrm\Concerns\HasMetas;
use Dbout\WpOrm\MetaMappingConfig;
use Dbout\WpOrm\Models\Meta\PostMeta;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

/**
 * @method Post setPostDate($date)
 * @method Carbon|null getPostDate()
 * @method Post setPostDateGMT($date)
 * @method Carbon|null getPostDateGMT()
 * @method Post setPostContent(?string $content)
 * @method string|null getPostContent()
 * @method Post setPostType(string $type)
 * @method string|null getPostType()
 * @method Post setGuid(?string $guid)
 * @method string|null getGuid()
 * @method Post setPostTitle(?string $title)
 * @method string|null getPostTitle()
 * @method Post setPostExcerpt(?string $excerpt)
 * @method string|null getPostExcerpt()
 * @method Post setPostStatus(?string $status)
 * @method string|null getPostStatus()
 * @method Post setCommentStatus(string $status)
 * @method string|null getCommentStatus()
 * @method Post setPingStatus(string $status)
 * @method string|null getPingStatus()
 * @method Post setPostPassword(?string $password)
 * @method string|null getPostPassword()
 * @method Post setPostName(?string $name)
 * @method string|null getPostName()
 * @method Post setToPing(?string $toPing)
 * @method string|null getToPing()
 * @method Post setPinged(?string $pinged)
 * @method string|null getPinged()
 * @method Post setPostModified($modified)
 * @method Carbon|null getPostModified()
 * @method Post setPostModifiedGMT($modified)
 * @method Carbon|null getPostModifiedGMT()
 * @method setPostMimeType(?string $mimeType)
 * @method string|null getPostMimeType()
 * @method Post setMenuOrder(?int $order)
 * @method int|null getMenuOrder()
 * @method Post setPostContentFiltered($content)
 * @method string|null getPostContentFiltered()
 * @method Post setPostParent(?int $parentId)
 * @method int|null getPostParent()
 * @method Post setPostAuthor(?int $authorId)
 * @method int|null getPostAuthor()
 * @method static PostBuilder query()
 *
 * @property-read User|null $author
 * @property-read Collection<PostMeta> $metas
 * @property-read static|null $parent
 * @property-read Collection<Comment> $comments
 */
class Post extends AbstractModel implements WithMetaModelInterface
{
    use HasMetas;

    public const UPDATED_AT = self::MODIFIED;
    public const CREATED_AT = self::DATE;
    final public const POST_ID = 'ID';
    final public const AUTHOR = 'post_author';
    final public const DATE = 'post_date';
    final public const DATE_GMT = 'post_date_gmt';
    final public const CONTENT = 'post_content';
    final public const TITLE = 'post_title';
    final public const EXCERPT = 'post_excerpt';
    final public const COMMENT_STATUS = 'comment_status';
    final public const STATUS = 'post_status';
    final public const PING_STATUS = 'ping_status';
    final public const PASSWORD = 'post_password';
    final public const POST_NAME = 'post_name';
    final public const TO_PING = 'to_ping';
    final public const PINGED = 'pinged';
    final public const MODIFIED = 'post_modified';
    final public const MODIFIED_GMT = 'post_modified_gmt';
    final public const CONTENT_FILTERED = 'post_content_filtered';
    final public const PARENT = 'post_parent';
    final public const GUID = 'guid';
    final public const MENU_ORDER = 'menu_order';
    final public const TYPE = 'post_type';
    final public const MIME_TYPE = 'post_mime_type';
    final public const COMMENT_COUNT = 'comment_count';

    /**
     * @inheritDoc
     */
    protected $primaryKey = self::POST_ID;

    /**
     * @inheritDoc
     */
    protected $casts = [
        self::AUTHOR => 'integer',
        self::PARENT => 'integer',
        self::MENU_ORDER => 'integer',
        self::COMMENT_COUNT => 'integer',
        self::DATE => 'datetime',
        self::MODIFIED => 'datetime',
        self::DATE_GMT => 'datetime',
        self::MODIFIED_GMT => 'datetime',
    ];

    /**
     * @var string
     */
    protected $table = 'posts';

    /**
     * @return HasOne
     */
    public function author(): HasOne
    {
        return $this->hasOne(User::class, User::USER_ID, self::AUTHOR);
    }

    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, Comment::POST_ID);
    }

    /**
     * @return HasOne
     */
    public function parent(): HasOne
    {
        return $this->hasOne(Post::class, Post::POST_ID, self::PARENT);
    }

    /**
     * @inheritDoc
     */
    public function newEloquentBuilder($query): PostBuilder
    {
        return new PostBuilder($query);
    }

    /**
     * @param string|null $name
     * @return Post|null
     */
    public static function findOneByName(?string $name): ?Post
    {
        /** @var Post|null $model */
        $model = self::query()->firstWhere(self::POST_NAME, $name);
        return $model;
    }

    /**
     * @param string $guid
     * @return Post|null
     */
    public static function findOneByGuid(string $guid): ?Post
    {
        /** @var Post|null $model */
        $model = self::query()->firstWhere(self::GUID, $guid);
        return $model;
    }

    /**
     * @return MetaMappingConfig
     */
    public function getMetaConfigMapping(): MetaMappingConfig
    {
        return new MetaMappingConfig(PostMeta::class, PostMeta::POST_ID);
    }
}
