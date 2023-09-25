<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Carbon\Carbon;
use Dbout\WpOrm\Builders\PostBuilder;
use Dbout\WpOrm\Models\Meta\PostMeta;
use Dbout\WpOrm\Models\Meta\WithMeta;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @method static Post find(int $postId)
 * @method static PostBuilder query()
 * @property User|null $author
 * @property PostMeta[] $metas
 * @property Post|null $parent
 *
 * @method self setPostDate($date)
 * @method Carbon|null getPostDate()
 * @method self setPostDateGMT($date)
 * @method Carbon|null getPostDateGMT()
 * @method self setPostContent(?string $content)
 * @method string|null getPostContent()
 * @method self setPostType(string $type)
 * @method string|null getPostType()
 * @method self setGuid(?string $guid)
 * @method string|null getGuid()
 * @method self setPostTitle(?string $title)
 * @method string|null getPostTitle()
 * @method self setPostExcerpt(?string $excerpt)
 * @method string|null getPostExcerpt()
 * @method self setPostStatus(?string $status)
 * @method string|null getPostStatus()
 * @method self setCommentStatus(string $status)
 * @method string|null getCommentStatus()
 * @method self setPingStatus(string $status)
 * @method string|null getPingStatus()
 * @method self setPostPassword(?string $password)
 * @method string|null getPostPassword()
 * @method self setPostName(?string $name)
 * @method string|null getPostName()
 * @method self setToPing(?string $toPing)
 * @method string|null getToPing()
 * @method self setPinged(?string $pinged)
 * @method string|null getPinged()
 * @method self setPostModified($modified)
 * @method Carbon|null getPostModified()
 * @method self setPostModifiedGMT($modified)
 * @method Carbon|null getPostModifiedGMT()
 * @method setPostMimeType(?string $mimeType)
 * @method string|null getPostMimeType()
 * @method self setMenuOrder(?int $order)
 * @method int|null getMenuOrder()
 * @method self setPostContentFiltered($content)
 * @method string|null getPostContentFiltered()
 */
class Post extends AbstractModel
{
    use WithMeta;

    public const CREATED_AT = 'post_date';
    public const UPDATED_AT = 'post_modified';
    public const POST_ID = 'ID';
    public const AUTHOR = 'post_author';
    public const DATE = 'post_date';
    public const DATE_GMT = 'post_date_gmt';
    public const CONTENT = 'post_content';
    public const TITLE = 'post_title';
    public const EXCERPT = 'post_excerpt';
    public const COMMENT_STATUS = 'comment_status';
    public const STATUS = 'post_status';
    public const PING_STATUS = 'ping_status';
    public const PASSWORD = 'post_password';
    public const POST_NAME = 'post_name';
    public const TO_PING = 'to_ping';
    public const PINGED = 'pinged';
    public const MODIFIED = 'post_modified';
    public const MODIFIED_GMT = 'post_modified_gmt';
    public const CONTENT_FILTERED = 'post_content_filtered';
    public const PARENT = 'post_parent';
    public const GUID = 'guid';
    public const MENU_ORDER = 'menu_order';
    public const TYPE = 'post_type';
    public const MIME_TYPE = 'post_mime_type';
    public const COMMENT_COUNT = 'comment_count';

    /**
     * @var string
     */
    protected $primaryKey = self::POST_ID;

    /**
     * @var string[]
     */
    protected $dates = [
        self::DATE, self::MODIFIED, self::DATE_GMT,  self::MODIFIED_GMT,
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        self::CONTENT, self::TITLE, self::EXCERPT, self::COMMENT_STATUS, self::STATUS,
        self::PING_STATUS, self::PASSWORD, self::POST_NAME, self::TO_PING, self::PINGED,
        self::CONTENT_FILTERED, self::PARENT, self::GUID, self::MENU_ORDER, self::TYPE,
        self::MIME_TYPE, self::COMMENT_COUNT,
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        self::MENU_ORDER => 'integer',
        self::COMMENT_COUNT => 'integer',
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
     * @inerhitDoc
     */
    public function getMetaClass(): string
    {
        return \Dbout\WpOrm\Models\Meta\PostMeta::class;
    }
}
