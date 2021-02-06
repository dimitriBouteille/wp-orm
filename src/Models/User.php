<?php

namespace Dbout\WpOrm\Models;

use Carbon\Carbon;
use Dbout\WpOrm\Builders\UserBuilder;
use Dbout\WpOrm\Models\Meta\ModelWithMetas;
use Dbout\WpOrm\Models\Meta\UserMeta;
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
 */
class User extends AbstractModel
{

    use ModelWithMetas;

    const USER_ID = 'ID';
    const LOGIN = 'user_login';
    const PASSWORD = 'user_pass';
    const NICE_NAME = 'user_nicename';
    const EMAIL = 'user_email';
    const URL = 'user_url';
    const REGISTERED = 'user_registered';
    const ACTIVATION_KEY = 'user_activation_key';
    const DISPLAY_NAME = 'display_name';
    const STATUS = 'status';
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
     * @return string|null
     */
    public function getLogin(): ?string
    {
        return $this->getAttribute(self::LOGIN);
    }

    /**
     * @param string|null $login
     * @return $this
     */
    public function setLogin(?string $login): self
    {
        $this->setAttribute(self::LOGIN, $login);
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
    public function getEmail(): ?string
    {
        return $this->getAttribute(self::EMAIL);
    }

    /**
     * @param string|null $email
     * @return $this
     */
    public function setEmail(?string $email): User
    {
        $this->setAttribute(self::EMAIL, $email);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDisplayName(): ?string
    {
        return $this->getAttribute(self::DISPLAY_NAME);
    }

    /**
     * @param string|null $displayName
     * @return $this
     */
    public function setDisplayName(?string $displayName): User
    {
        $this->setAttribute(self::DISPLAY_NAME, $displayName);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->getAttribute(self::URL);
    }

    /**
     * @param string|null $url
     * @return $this
     */
    public function setUrl(?string $url): User
    {
        $this->setAttribute(self::URL, $url);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNiceName(): ?string
    {
        return $this->getAttribute(self::NICE_NAME);
    }

    /**
     * @param string|null $niceName
     * @return $this
     */
    public function setNiceName(?string $niceName): self
    {
        $this->setAttribute(self::NICE_NAME, $niceName);
        return $this;
    }

    /**
     * @return $this
     */
    public function getStatus(): self
    {
        return $this->getAttribute(self::STATUS);
    }

    /**
     * @param $status
     * @return $this
     */
    public function setStatus($status): self
    {
        $this->setAttribute(self::STATUS, $status);
        return $this;
    }

    /**
     * @return Carbon|null
     */
    public function getRegistered(): ?Carbon
    {
        return $this->getAttribute(self::REGISTERED);
    }

    /**
     * @param $registered
     * @return $this
     */
    public function setRegistered($registered): self
    {
        $this->setAttribute(self::REGISTERED, $registered);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getActivationKey(): ?string
    {
        return $this->getAttribute(self::ACTIVATION_KEY);
    }

    /**
     * @param string|null $activationKey
     * @return $this
     */
    public function setActivationKey(?string $activationKey): self
    {
        $this->setAttribute(self::ACTIVATION_KEY, $activationKey);
        return $this;
    }

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
        return $this->hasMany(Post::class, Post::POST_AUTHOR);
    }

    /**
     * @return string
     */
    protected function _getMetaClass(): string
    {
        return UserMeta::class;
    }

    /**
     * @return string
     */
    protected function _getMetaFk(): string
    {
        return UserMeta::USER_ID;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return UserBuilder
     */
    public function newEloquentBuilder($query): UserBuilder
    {
        return new UserBuilder($query);
    }
}
