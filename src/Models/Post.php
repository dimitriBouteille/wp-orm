<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Api\CommentInterface;
use Dbout\WpOrm\Api\PostInterface;
use Dbout\WpOrm\Api\UserInterface;
use Dbout\WpOrm\Builders\PostBuilder;
use Dbout\WpOrm\Enums\PingStatus;
use Dbout\WpOrm\Enums\PostStatus;
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
 * @method static|PostBuilder author(int|User $user)
 * @method static|PostBuilder status(string|PostStatus $status)
 * @method static|PostBuilder pingStatus(string|PingStatus $status)
 * @method static|PostBuilder postType(string $type)
 */
class Post extends AbstractModel implements PostInterface
{
    use WithMeta;

    public const UPDATED_AT = self::MODIFIED;
    public const CREATED_AT = self::DATE;

    /**
     * @inheritDoc
     */
    protected $primaryKey = self::POST_ID;

    /**
     * @inheritDoc
     */
    protected $fillable = [
        self::CONTENT,
        self::TITLE,
        self::EXCERPT,
        self::COMMENT_STATUS,
        self::STATUS,
        self::PING_STATUS,
        self::PASSWORD,
        self::POST_NAME,
        self::TO_PING,
        self::PINGED,
        self::CONTENT_FILTERED,
        self::PARENT,
        self::GUID,
        self::MENU_ORDER,
        self::TYPE,
        self::MIME_TYPE,
        self::COMMENT_COUNT,
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
     * @inheritDoc
     */
    public function getMetaClass(): string
    {
        return \Dbout\WpOrm\Models\Meta\PostMeta::class;
    }

    /**
     * @param PostBuilder $builder
     * @param int|User $user
     * @return void
     */
    public function scopeAuthor(PostBuilder $builder, int|User $user): void
    {
        if ($user instanceof User) {
            $user = $user->getId();
        }

        $builder->where(self::AUTHOR, $user);
    }

    /**
     * @param PostBuilder $builder
     * @param string|PostStatus $status
     * @return void
     */
    public function scopeStatus(PostBuilder $builder, string|PostStatus $status): void
    {
        if ($status instanceof PostStatus) {
            $status = $status->value;
        }

        $builder->where(self::STATUS, $status);
    }

    /**
     * @param PostBuilder $builder
     * @param string|PingStatus $status
     * @return void
     */
    public function scopePingStatus(PostBuilder $builder, string|PingStatus $status): void
    {
        if ($status instanceof PingStatus) {
            $status = $status->value;
        }

        $builder->where(self::PING_STATUS, $status);
    }

    /**
     * @param PostBuilder $builder
     * @param string $postType
     * @return void
     */
    public function scopePostType(PostBuilder $builder, string $postType): void
    {
        $builder->where(self::TYPE, $postType);
    }

    /**
     * @inheritDoc
     */
    public function findOneByName(?string $name): ?Post
    {
        /** @var Post|null $model */
        $model = self::query()->firstWhere(self::POST_NAME, $name);
        return $model;
    }
}
