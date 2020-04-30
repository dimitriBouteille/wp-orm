<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Contracts\PostInterface;
use Illuminate\Database\Eloquent\Builder;
use Dbout\WpOrm\Orm\AbstractModel;

/**
 * Class Post
 * @package Dbout\WpOrm\Models
 *
 * @method static Builder author(int $author);
 * @method static Builder type($types);
 * @method static Builder status($status);
 * @method static PostInterface find($postId);
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
     * @return int|null
     */
    public function getAuthor(): ?int
    {
        return (int)$this->getAttribute(self::POST_EXCERPT);
    }

    /**
     * @param int|null $author
     * @return PostInterface
     */
    public function setAuthor(?int $author): PostInterface
    {
        $this->setAttribute(self::POST_AUTHOR, $author);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->getAttribute(self::CREATED_AT);
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getAttribute(self::UPDATED_AT);
    }

    /**
     * @return Builder
     */
    public function newQuery()
    {
        $query = parent::newQuery();

        // Select by default only publish post
//        $query->where(Post::POST_STATUS, 'publish');

        return $query;
    }

    /**
     * @return string|null
     */
    public function getPostType(): ?string
    {
        return $this->getAttribute(self::POST_TYPE);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getMetas()
    {
        return $this->hasMany(PostMeta::class, PostMeta::POST_ID);
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
     * Filter by post author
     *
     * @param Builder $query
     * @param null $author
     * @return Builder
     */
    public function scopeAuthor(Builder $query, $author = null)
    {
        return $query->where('post_author', '=', $author);
    }

    /**
     * Filter by post type
     *
     * @param Builder $query
     * @param string|array $postType
     * @return Builder
     */
    public function scopeType(Builder $query, $postType)
    {
        if(is_array($postType)) {
            return $query->whereIn('post_type', $postType);
        }

        return $query->where('post_type', '=', $postType);
    }

    /**
     * Filter by post status
     *
     * @param Builder $query
     * @param string|array $status
     * @return Builder
     */
    public function scopeStatus(Builder $query, $status = 'publish')
    {
        if(is_array($status)) {
            return $query->whereIn('post_status', $status);
        }

        return $query->where('post_status', '=', $status);
    }

}