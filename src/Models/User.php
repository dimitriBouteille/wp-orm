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
use Dbout\WpOrm\Builders\UserBuilder;
use Dbout\WpOrm\Models\Meta\UserMeta;
use Dbout\WpOrm\Models\Meta\WithMeta;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static static|null find($userId)
 * @method static UserBuilder query()
 * @property-read UserMeta[] $metas
 * @property-read Comment[] $comments
 * @property-read Post[] $posts
 */
#[\Dbout\WpOrm\Attributes\MetaConfigAttribute(UserMeta::class, UserMeta::USER_ID)]
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
    protected $guarded = [];

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
    public static function findOneByEmail(string $email): ?self
    {
        /** @var self|null $result */
        $result = self::query()->firstWhere(self::EMAIL, $email);
        return $result;
    }

    /**
     * @inheritDoc
     */
    public static function findOneByLogin(string $login): ?self
    {
        /** @var self|null $result */
        $result = self::query()->firstWhere(self::LOGIN, $login);
        return $result;
    }
}
