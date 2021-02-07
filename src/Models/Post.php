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
 * @method static Post find($postId)
 * @method static PostBuilder query()
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
     * @return mixed|void
     */
    public function getDate(): ?Carbon
    {
        return $this->getAttribute(self::DATE);
    }

    /**
     * @param $date
     * @return $this
     */
    public function setDate($date): self
    {
        $this->setAttribute(self::DATE, $date);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateGMT()
    {
        return $this->getAttribute(self::DATE_GMT);
    }

    /**
     * @param $date
     * @return $this
     */
    public function setDateGMT($date): self
    {
        $this->setAttribute(self::DATE_GMT, $date);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->getAttribute(self::CONTENT);
    }

    /**
     * @param string|null $content
     * @return $this
     */
    public function setContent(?string $content): self
    {
        $this->setAttribute(self::CONTENT, $content);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->getAttribute(self::TITLE);
    }

    /**
     * @param string|null $title
     * @return $this
     */
    public function setTitle(?string $title): self
    {
        $this->setAttribute(self::TITLE, $title);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getExcerpt(): ?string
    {
        return $this->getAttribute(self::EXCERPT);
    }

    /**
     * @param string|null $excerpt
     * @return $this
     */
    public function setExcerpt(?string $excerpt): self
    {
        $this->setAttribute(self::EXCERPT, $excerpt);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->getAttribute(self::STATUS);
    }

    /**
     * @param string|null $status
     * @return $this
     */
    public function setStatus(?string $status): self
    {
        $this->setAttribute(self::STATUS, $status);
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
        return $this->getAttribute(self::PASSWORD);
    }

    /**
     * @param string|null $password
     * @return $this
     */
    public function setPassword(?string $password): self
    {
        $this->setAttribute(self::PASSWORD, $password);
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
     * @return Carbon|null
     */
    public function getModified(): ?Carbon
    {
        return $this->getAttribute(self::MODIFIED);
    }

    /**
     * @param $modified
     * @return $this
     */
    public function setModified($modified): self
    {
        $this->setAttribute(self::MODIFIED, $modified);
        return $this;
    }

    /**
     * @return Carbon|null
     */
    public function getMdifiedGMT(): ?Carbon
    {
        return $this->getAttribute(self::MODIFIED_GMT);
    }

    /**
     * @param $modified
     * @return $this
     */
    public function setModifiedGMT($modified): self
    {
        $this->setAttribute(self::MODIFIED_GMT, $modified);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->getAttribute(self::TYPE);
    }

    /**
     * @param string $postType
     * @return $this
     */
    public function setType(string $postType): self
    {
        $this->setAttribute(self::TYPE, $postType);
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
        return $this->getAttribute(self::MIME_TYPE);
    }

    /**
     * @param string|null $mimeType
     * @return $this
     */
    public function setMimeType(?string $mimeType): self
    {
        $this->setAttribute(self::MIME_TYPE, $mimeType);
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

    /**
     * @return string
     */
    protected function _getMetaFk(): string
    {
        return PostMeta::POST_ID;
    }
}
