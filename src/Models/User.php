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
use Dbout\WpOrm\Builders\UserBuilder;
use Dbout\WpOrm\Models\Meta\UserMeta;
use Dbout\WpOrm\Models\Meta\WithMeta;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static User|null find($userId)
 * @method static UserBuilder query()
 * @property UserMeta[] $metas
 * @property Comment[] $comments
 * @property Post[] $posts
 */
class User extends AbstractModel implements UserInterface
{
    use WithMeta;

    public const CREATED_AT = self::REGISTERED;
    public const UPDATED_AT = null;

    /**
     * @inheritDoc
     */
    protected $table = 'users';

    /**
     * @inheritDoc
     */
    protected $casts = [
        self::STATUS => 'integer',
        self::REGISTERED => 'datetime',
    ];

    /**
     * @inheritDoc
     */
    protected $primaryKey = self::USER_ID;

    /**
     * @inheritDoc
     */
    protected $fillable = [
        self::LOGIN,
        self::PASSWORD,
        self::NICE_NAME,
        self::EMAIL,
        self::URL,
        self::REGISTERED,
        self::ACTIVATION_KEY,
        self::DISPLAY_NAME,
        self::STATUS,
    ];

    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, CommentInterface::USER_ID);
    }

    /**
     * @return HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, PostInterface::AUTHOR);
    }

    /**
     * @inheritDoc
     */
    public function newEloquentBuilder($query): UserBuilder
    {
        return new UserBuilder($query);
    }

    /**
     * @inheritDoc
     */
    public function getMetaClass(): string
    {
        return \Dbout\WpOrm\Models\Meta\UserMeta::class;
    }

    /**
     * @inheritDoc
     */
    public static function findByEmail(string $email): ?self
    {
        return self::query()->firstWhere(self::EMAIL, $email);
    }

    /**
     * @inheritDoc
     */
    public static function findByLogin(string $login): ?self
    {
        return self::query()->firstWhere(self::LOGIN, $login);
    }
}
