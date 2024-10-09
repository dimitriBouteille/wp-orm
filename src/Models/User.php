<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Carbon\Carbon;
use Dbout\WpOrm\Api\WithMetaModelInterface;
use Dbout\WpOrm\Builders\UserBuilder;
use Dbout\WpOrm\Concerns\HasMetas;
use Dbout\WpOrm\MetaMappingConfig;
use Dbout\WpOrm\Models\Meta\UserMeta;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @method string|null getUserLogin()
 * @method User setUserLogin(string $login)
 * @method string|null getUserPass()
 * @method User setUserPass(string $password)
 * @method string|null getUserNicename()
 * @method User setUserNicename(string $nicename)
 * @method string|null getUserEmail()
 * @method User setUserEmail(string $email)
 * @method string|null getUserUrl()
 * @method User setUserUrl(?string $url)
 * @method Carbon|null getUserRegistered()
 * @method User setUserRegistered($date)
 * @method string|null getUserActivationKey()
 * @method User setUserActivationKey(?string $key)
 * @method int getUserStatus()
 * @method User setUserStatus(int $status)
 * @method string|null getDisplayName()
 * @method User setDisplayName(?string $name)
 * @method static UserBuilder query()
 *
 * @property-read Collection<UserMeta> $metas
 * @property-read Collection<Comment> $comments
 * @property-read Collection<Post> $posts
 */
class User extends AbstractModel implements WithMetaModelInterface
{
    use HasMetas;

    final public const CREATED_AT = self::REGISTERED;
    final public const UPDATED_AT = null;

    final public const USER_ID = 'ID';
    final public const LOGIN = 'user_login';
    final public const PASSWORD = 'user_pass';
    final public const NICE_NAME = 'user_nicename';
    final public const EMAIL = 'user_email';
    final public const URL = 'user_url';
    final public const REGISTERED = 'user_registered';
    final public const ACTIVATION_KEY = 'user_activation_key';
    final public const DISPLAY_NAME = 'display_name';
    final public const STATUS = 'user_status';

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
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, Comment::USER_ID);
    }

    /**
     * @return HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, Post::AUTHOR);
    }

    /**
     * @inheritDoc
     */
    public function newEloquentBuilder($query): UserBuilder
    {
        return new UserBuilder($query);
    }

    /**
     * @param string $email
     * @return self|null
     */
    public static function findOneByEmail(string $email): ?self
    {
        /** @var self|null $result */
        $result = self::query()->firstWhere(self::EMAIL, $email);
        return $result;
    }

    /**
     * @param string $login
     * @return self|null
     */
    public static function findOneByLogin(string $login): ?self
    {
        /** @var self|null $result */
        $result = self::query()->firstWhere(self::LOGIN, $login);
        return $result;
    }

    /**
     * @return MetaMappingConfig
     */
    public function getMetaConfigMapping(): MetaMappingConfig
    {
        return new MetaMappingConfig(UserMeta::class, UserMeta::USER_ID);
    }
}
