<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Contracts\CommentInterface;
use Dbout\WpOrm\Contracts\UserInterface;
use Dbout\WpOrm\Contracts\UserMetaInterface;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class User
 * @package Dbout\WpOrm\Models
 *
 * @method static UserInterface find($userId);
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
class User extends AbstractModel implements UserInterface
{

    const CREATED_AT = 'user_registered';
    const UPDATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * @return string|null
     */
    public function getLogin(): ?string
    {
        return $this->getAttribute(self::USER_LOGIN);
    }

    /**
     * @param string|null $login
     * @return UserInterface
     */
    public function setLogin(?string $login): UserInterface
    {
        $this->setAttribute(self::USER_LOGIN, $login);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->getAttribute(self::USER_PASS);
    }

    /**
     * @param string|null $password
     * @return UserInterface
     */
    public function setPassword(?string $password): UserInterface
    {
        $this->setAttribute(self::USER_PASS, $password);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->getAttribute(self::USER_EMAIL);
    }

    /**
     * @param string|null $email
     * @return UserInterface
     */
    public function setEmail(?string $email): UserInterface
    {
        $this->setAttribute(self::USER_EMAIL, $email);
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
     * @return UserInterface
     */
    public function setDisplayName(?string $displayName): UserInterface
    {
        $this->setAttribute(self::DISPLAY_NAME, $displayName);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->getAttribute(self::USER_URL);
    }

    /**
     * @param string|null $url
     * @return UserInterface
     */
    public function setUrl(?string $url): UserInterface
    {
        $this->setAttribute(self::USER_URL, $url);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNiceName(): ?string
    {
        return $this->getAttribute(self::USER_NICENAME);
    }

    /**
     * @param string|null $niceName
     * @return UserInterface
     */
    public function setNiceName(?string $niceName): UserInterface
    {
        $this->setAttribute(self::USER_NICENAME, $niceName);
        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getRegistered(): ?\DateTimeInterface
    {
        return $this->getAttribute(self::USER_REGISTERED);
    }

    /**
     * @param $registered
     * @return UserInterface
     */
    public function setRegistered($registered): UserInterface
    {
        $this->setAttribute(self::USER_REGISTERED, $registered);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getActivationKey(): ?string
    {
        return $this->getAttribute(self::USER_ACTIVATION_KEY);
    }

    /**
     * @param string|null $activationKey
     * @return UserInterface
     */
    public function setActivationKey(?string $activationKey): UserInterface
    {
        $this->setAttribute(self::USER_ACTIVATION_KEY, $activationKey);
        return $this;
    }

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
    public function metas(): HasMany
    {
        return $this->hasMany(UserMeta::class, UserMetaInterface::USER_ID);
    }

}