<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Carbon\Carbon;
use Dbout\WpOrm\Api\CommentInterface;
use Dbout\WpOrm\Api\PostInterface;
use Dbout\WpOrm\Api\UserInterface;
use Dbout\WpOrm\Builders\PostBuilder;
use Dbout\WpOrm\Concerns\HasMeta;
use Dbout\WpOrm\Models\Meta\PostMeta;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
 * @method static static find(int $postId)
 * @method static PostBuilder query()
 *
 * @property-read User|null $author
 * @property-read PostMeta[] $metas
 * @property-read static|null $parent
 * @property-read Comment[] $comments
 */
#[\Dbout\WpOrm\Attributes\MetaConfigAttribute(PostMeta::class, PostMeta::POST_ID)]
class Post extends AbstractModel implements PostInterface
{
    use HasMeta;

    public const UPDATED_AT = self::MODIFIED;
    public const CREATED_AT = self::DATE;

    /**
     * @inheritDoc
     */
    protected $primaryKey = self::POST_ID;

    /**
     * @inheritDoc
     */
    protected $casts = [
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
        return $this->hasOne(User::class, UserInterface::USER_ID, self::AUTHOR);
    }

    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, CommentInterface::POST_ID);
    }

    /**
     * @return HasOne
     */
    public function parent(): HasOne
    {
        return $this->hasOne(Post::class, PostInterface::POST_ID, self::PARENT);
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
    public function findOneByName(?string $name): ?Post
    {
        /** @var Post|null $model */
        $model = self::query()->firstWhere(self::POST_NAME, $name);
        return $model;
    }
}
