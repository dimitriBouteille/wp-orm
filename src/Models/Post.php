<?php

namespace Dbout\WpOrm\Models;

use Carbon\Carbon;
use Dbout\WpOrm\Builders\PostBuilder;
use Dbout\WpOrm\Models\Meta\ModelWithMetas;
use Dbout\WpOrm\Models\Meta\PostMeta;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Post
 * @package Dbout\WpOrm\Models
 *
 * @method static self find($postId);
 * @method static PostBuilder query();
 * @property User|null $author
 * @property PostMeta[] $metas
 * @property Post|null $parent
 */
class Post extends AbstractModel
{

    use ModelWithMetas;

    const CREATED_AT = 'post_date';
    const UPDATED_AT = 'post_modified';
    const POST_ID = 'ID';
    const POST_AUTHOR = 'post_author';
    const POST_DATE = 'post_date';
    const POST_DATE_GMT = 'post_date_gmt';
    const POST_CONTENT = 'post_content';
    const POST_TITLE = 'post_title';
    const POST_EXCERPT = 'post_excerpt';
    const COMMENT_STATUS = 'comment_status';
    const POST_STATUS = 'post_status';
    const PING_STATUS = 'ping_status';
    const POST_PASSWORD = 'post_password';
    const POST_NAME = 'post_name';
    const TO_PING = 'to_ping';
    const PINGED = 'pinged';
    const POST_MODIFIED = 'post_modified';
    const POST_MODIFIED_GMT = 'post_modified_gmt';
    const POST_CONTENT_FILTERED = 'post_content_filtered';
    const POST_PARENT = 'post_parent';
    const GUID = 'guid';
    const MENU_ORDER = 'menu_order';
    const POST_TYPE = 'post_type';
    const POST_MIME_TYPE = 'post_mime_type';
    const COMMENT_COUNT = 'comment_count';

    /**
     * @var string
     */
    protected $primaryKey = self::POST_ID;

    /**
     * @var string[]
     */
    protected $dates = [
        self::POST_DATE
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        self::POST_CONTENT, self::POST_TITLE, self::POST_EXCERPT, self::COMMENT_STATUS, self::POST_STATUS,
        self::PING_STATUS, self::POST_PASSWORD, self::POST_NAME, self::TO_PING, self::PINGED,
        self::POST_CONTENT_FILTERED, self::POST_PARENT, self::GUID, self::MENU_ORDER, self::POST_TYPE,
        self::POST_MIME_TYPE, self::COMMENT_COUNT,
    ];

    /**
     * @var string
     */
    protected $table = 'posts';

    /**
     * @return mixed|void
     */
    public function getDate(): ?Carbon
    {
        return $this->getAttribute(self::POST_DATE);
    }

    /**
     * @param $date
     * @return $this
     */
    public function setDate($date): self
    {
        $this->setAttribute(self::POST_DATE, $date);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateGMT()
    {
        return $this->getAttribute(self::POST_DATE_GMT);
    }

    /**
     * @param $date
     * @return $this
     */
    public function setDateGMT($date): self
    {
        $this->setAttribute(self::POST_DATE_GMT, $date);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->getAttribute(self::POST_CONTENT);
    }

    /**
     * @param string|null $content
     * @return $this
     */
    public function setContent(?string $content): self
    {
        $this->setAttribute(self::POST_CONTENT, $content);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->getAttribute(self::POST_TITLE);
    }

    /**
     * @param string|null $title
     * @return $this
     */
    public function setTitle(?string $title): self
    {
        $this->setAttribute(self::POST_TITLE, $title);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getExcerpt(): ?string
    {
        return $this->getAttribute(self::POST_EXCERPT);
    }

    /**
     * @param string|null $excerpt
     * @return $this
     */
    public function setExcerpt(?string $excerpt): self
    {
        $this->setAttribute(self::POST_EXCERPT, $excerpt);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->getAttribute(self::POST_STATUS);
    }

    /**
     * @param string|null $status
     * @return $this
     */
    public function setStatus(?string $status): self
    {
        $this->setAttribute(self::POST_STATUS, $status);
        return $this;
    }

    /**
     * @return string
     */
    public function getCommentStatus(): string
    {
        return $this->getAttribute(self::COMMENT_STATUS);
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setCommentStatus(string $status): self
    {
        $this->setAttribute(self::COMMENT_STATUS, $status);
        return $this;
    }

    /**
     * @return string
     */
    public function getPingStatus(): string
    {
        return $this->getAttribute(self::PING_STATUS);
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setPingStatus(string $status): self
    {
        $this->setAttribute(self::PING_STATUS, $status);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->getAttribute(self::POST_PASSWORD);
    }

    /**
     * @param string|null $password
     * @return $this
     */
    public function setPassword(?string $password): self
    {
        $this->setAttribute(self::POST_PASSWORD, $password);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getAttribute(self::POST_NAME);
    }

    /**
     * @param string|null $name
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->setAttribute(self::POST_NAME, $name);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getToPing(): ?string
    {
        return $this->getAttribute(self::TO_PING);
    }

    /**
     * @param string|null $toPing
     * @return $this
     */
    public function setToPing(?string $toPing): self
    {
        $this->setAttribute(self::TO_PING, $toPing);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPinged(): ?string
    {
        return $this->getAttribute(self::PINGED);
    }

    /**
     * @param string|null $pinged
     * @return $this
     */
    public function setPinged(?string $pinged): self
    {
        $this->setAttribute(self::PINGED, $pinged);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getModified()
    {
        return $this->getAttribute(self::POST_MODIFIED);
    }

    /**
     * @param $modified
     * @return $this
     */
    public function setModified($modified): self
    {
        $this->setAttribute(self::POST_MODIFIED, $modified);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostModifiedGMT()
    {
        return $this->getAttribute(self::POST_MODIFIED_GMT);
    }

    /**
     * @param $modified
     * @return $this
     */
    public function setPostModifiedGMT($modified): self
    {
        $this->setAttribute(self::POST_MODIFIED_GMT, $modified);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPostType(): ?string
    {
        return $this->getAttribute(self::POST_TYPE);
    }

    /**
     * @param string $postType
     * @return $this
     */
    public function setPostType(string $postType): self
    {
        $this->setAttribute(self::POST_TYPE, $postType);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGuid(): ?string
    {
        return $this->getAttribute(self::GUID);
    }

    /**
     * @param string|null $guid
     * @return $this
     */
    public function setGuid(?string $guid): self
    {
        $this->setAttribute(self::GUID, $guid);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMimeType(): ?string
    {
        return $this->getAttribute(self::POST_MIME_TYPE);
    }

    /**
     * @param string|null $mimeType
     * @return $this
     */
    public function setMimeType(?string $mimeType): self
    {
        $this->setAttribute(self::POST_MIME_TYPE, $mimeType);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMenuOrder(): ?int
    {
        return $this->getAttribute(self::MENU_ORDER);
    }

    /**
     * @param int|null $order
     * @return $this
     */
    public function setMenuOrder(?int $order): self
    {
        $this->setAttribute(self::MENU_ORDER, $order);
        return $this;
    }

    /**
     * @return HasOne
     */
    public function author(): HasOne
    {
        return $this->hasOne(User::class, User::USER_ID, self::POST_AUTHOR);
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
        return $this->hasOne(Post::class, Post::POST_ID, self::POST_PARENT);
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return PostBuilder
     */
    public function newEloquentBuilder($query): PostBuilder
    {
        return new PostBuilder($query);
    }

    /**
     * @return string
     */
    protected function _getMetaClass(): string
    {
        return \Dbout\WpOrm\Models\Meta\PostMeta::class;
    }
}
