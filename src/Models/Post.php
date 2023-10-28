<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Api\CommentInterface;
use Dbout\WpOrm\Api\PostInterface;
use Dbout\WpOrm\Api\UserInterface;
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
 * @property Comment[] $comments
 */
class Post extends AbstractModel implements PostInterface
{
    use WithMeta;

    public const UPDATED_AT = self::MODIFIED;

    public const CREATED_AT = self::DATE;

    /**
     * @var string
     */
    protected $primaryKey = self::POST_ID;

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
     * @inerhitDoc
     */
    public function getMetaClass(): string
    {
        return \Dbout\WpOrm\Models\Meta\PostMeta::class;
    }
}
