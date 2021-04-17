<?php

namespace Dbout\WpOrm\Models;

use Carbon\Carbon;
use Dbout\WpOrm\Builders\PostBuilder;
use Dbout\WpOrm\Models\Meta\PostMeta;
use Dbout\WpOrm\Models\Meta\WithMeta;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Post
 * @package Dbout\WpOrm\Models
 *
 * @method static Post find(int $postId)
 * @method static PostBuilder query()
 * @property User|null $author
 * @property PostMeta[] $metas
 * @property Post|null $parent
 *
 * @method self setDate($date)
 * @method Carbon|null getDate()
 * @method self setDateGMT($date)
 * @method Carbon|null getDateGMT()
 * @method self setContent(?string $content)
 * @method string|null getContent()
 * @method self setType(string $type)
 * @method string|null getType()
 * @method self setGuid(?string $guid)
 * @method string|null getGuid()
 * @method self setTitle(?string $title)
 * @method string|null getTitle()
 * @method self setExcerpt(?string $excerpt)
 * @method string|null getExcerpt()
 * @method self setStatus(?string $status)
 * @method string|null getStatus()
 * @method self setCommentStatus(string $status)
 * @method string|null getCommentStatus()
 * @method self setPingStatus(string $status)
 * @method string|null getPingStatus()
 * @method self setPassword(?string $password)
 * @method string|null getPassword()
 * @method self setName(?string $name)
 * @method string|null getName()
 * @method self setToPing(?string $toPing)
 * @method string|null getToPing()
 * @method self setPinged(?string $pinged)
 * @method string|null getPinged()
 * @method self setModified($modified)
 * @method Carbon|null getModified()
 * @method self setModifiedGMT($modified)
 * @method Carbon|null getModifiedGMT()
 * @method setMimeType(?string $mimeType)
 * @method string|null getMimeType()
 * @method self setMenuOrder(?int $order)
 * @method int|null getMenuOrder()
 */
class Post extends AbstractModel
{

    use WithMeta;

    const CREATED_AT = 'post_date';
    const UPDATED_AT = 'post_modified';
    const POST_ID = 'ID';
    const AUTHOR = 'post_author';
    const DATE = 'post_date';
    const DATE_GMT = 'post_date_gmt';
    const CONTENT = 'post_content';
    const TITLE = 'post_title';
    const EXCERPT = 'post_excerpt';
    const COMMENT_STATUS = 'comment_status';
    const STATUS = 'post_status';
    const PING_STATUS = 'ping_status';
    const PASSWORD = 'post_password';
    const POST_NAME = 'post_name';
    const TO_PING = 'to_ping';
    const PINGED = 'pinged';
    const MODIFIED = 'post_modified';
    const MODIFIED_GMT = 'post_modified_gmt';
    const CONTENT_FILTERED = 'post_content_filtered';
    const PARENT = 'post_parent';
    const GUID = 'guid';
    const MENU_ORDER = 'menu_order';
    const TYPE = 'post_type';
    const MIME_TYPE = 'post_mime_type';
    const COMMENT_COUNT = 'comment_count';

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
