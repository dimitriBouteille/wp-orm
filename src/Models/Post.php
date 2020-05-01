<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Contracts\CommentInterface;
use Dbout\WpOrm\Contracts\PostInterface;
use Dbout\WpOrm\Contracts\PostMetaInterface;
use Dbout\WpOrm\Contracts\UserInterface;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Post
 * @package Dbout\WpOrm\Models
 *
 * @method static PostInterface find($postId);
 * @property UserInterface|null $author
 * @property CommentInterface[] $comments
 * @property PostMetaInterface[] $metas
 * @property PostInterface|null $parent
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
class Post extends AbstractModel implements PostInterface
{

    const CREATED_AT = 'post_date';
    const UPDATED_AT = 'post_modified';

    /**
     * @var string
     */
    protected $primaryKey = self::POST_ID;

    /**
     * @var string
     */
    protected $table = 'posts';

    /**
     * @return mixed|void
     */
    public function getDate()
    {
        return $this->getAttribute(self::POST_DATE);
    }

    /**
     * @param $date
     * @return PostInterface
     */
    public function setDate($date): PostInterface
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
     * @return PostInterface
     */
    public function setDateGMT($date): PostInterface
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
     * @return PostInterface
     */
    public function setContent(?string $content): PostInterface
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
     * @return PostInterface
     */
    public function setTitle(?string $title): PostInterface
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
     * @return PostInterface
     */
    public function setExcerpt(?string $excerpt): PostInterface
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
     * @return PostInterface
     */
    public function setStatus(?string $status): PostInterface
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
     * @return PostInterface
     */
    public function setCommentStatus(string $status): PostInterface
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
     * @return PostInterface
     */
    public function setPingStatus(string $status): PostInterface
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
     * @return PostInterface
     */
    public function setPassword(?string $password): PostInterface
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
     * @return PostInterface
     */
    public function setName(?string $name): PostInterface
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
     * @return PostInterface
     */
    public function setToPing(?string $toPing): PostInterface
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
     * @return PostInterface
     */
    public function setPinged(?string $pinged): PostInterface
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
     * @return PostInterface
     */
    public function setModified($modified): PostInterface
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
     * @return PostInterface
     */
    public function setPostModifiedGMT($modified): PostInterface
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
     * @return PostInterface
     */
    public function setPostType(string $postType): PostInterface
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
     * @return PostInterface
     */
    public function setGuid(?string $guid): PostInterface
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
     * @return PostInterface
     */
    public function setMimeType(?string $mimeType): PostInterface
    {
        $this->setAttribute(self::POST_MIME_TYPE, $mimeType);
        return $this;
    }

    /**
     * @return int
     */
    public function getMenuOrder(): int
    {
        return $this->getAttribute(self::MENU_ORDER);
    }

    /**
     * @param int $order
     * @return PostInterface
     */
    public function setMenuOrder(int $order): PostInterface
    {
        $this->setAttribute(self::MENU_ORDER, $order);
        return $this;
    }

    /**
     * @return HasMany
     */
    public function metas(): HasMany
    {
        return $this->hasMany(PostMeta::class, PostMetaInterface::POST_ID);
    }

    /**
     * @return HasOne
     */
    public function author(): HasOne
    {
        return $this->hasOne(User::class, UserInterface::USER_ID, self::POST_AUTHOR);
    }

    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, CommentInterface::COMMENT_POST_ID);
    }

    /**
     * @return HasOne
     */
    public function parent(): HasOne
    {
        return $this->hasOne(Post::class, PostInterface::POST_ID, self::POST_PARENT);
    }

}