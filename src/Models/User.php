<?php

namespace Dbout\WpOrm\Models;

use Carbon\Carbon;
use Dbout\WpOrm\Builders\UserBuilder;
use Dbout\WpOrm\Models\Meta\UserMeta;
use Dbout\WpOrm\Models\Meta\WithMeta;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class User
 * @package Dbout\WpOrm\Models
 *
 * @method static User|null find($userId)
 * @method static UserBuilder query()
 * @property UserMeta[] $metas
 * @property Comment $comments
 * @property Post[] $posts
 *
 * @method string|null getUserLogin()
 * @method self setUserLogin(string $login)
 * @method string|null getUserPass()
 * @method self setUserPass(string $password)
 * @method string|null getUserNicename()
 * @method self setUserNicename(string $nicename)
 * @method string|null getUserEmail()
 * @method self setUserEmail(string $email)
 * @method string|null getUserUrl()
 * @method self setUserUrl(?string $url)
 * @method Carbon|null getUserRegistered()
 * @method self setUserRegistered($date)
 * @method string|null getUserActivationKey()
 * @method self setUserActivationKey(?string $key)
 * @method int getUserStatus()
 * @method self setUserStatus(int $status)
 * @method string|null getDisplayName()
 * @method self setDisplayName(?string $name)
 *
 */
class User extends AbstractModel
{

    use WithMeta;

    const USER_ID = 'ID';
    const LOGIN = 'user_login';
    const PASSWORD = 'user_pass';
    const NICE_NAME = 'user_nicename';
    const EMAIL = 'user_email';
    const URL = 'user_url';
    const REGISTERED = 'user_registered';
    const ACTIVATION_KEY = 'user_activation_key';
    const DISPLAY_NAME = 'display_name';
    const STATUS = 'user_status';
    const CREATED_AT = 'user_registered';
    const UPDATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * @var string[]
     */
    protected $dates = [
        self::REGISTERED,
    ];

    /**
     * @var array
     */
    protected $casts = [
        self::STATUS => 'integer',
    ];

    /**
     * @var string
     */
    protected $primaryKey = self::USER_ID;

    /**
     * @var string[]
     */
    protected $fillable = [
        self::LOGIN, self::PASSWORD, self::NICE_NAME, self::EMAIL, self::URL, self::REGISTERED, self::ACTIVATION_KEY,
        self::DISPLAY_NAME, self::STATUS,
    ];

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
     * @inerhitDoc
     */
    public function getMetaClass(): string
    {
        return \Dbout\WpOrm\Models\Meta\UserMeta::class;
    }
}
