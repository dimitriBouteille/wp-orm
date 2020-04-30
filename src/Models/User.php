<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Contracts\CommentInterface;
use Dbout\WpOrm\Contracts\UserInterface;
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
        return $this->getAttribute(self::LOGIN);
    }

    /**
     * @param string|null $login
     * @return UserInterface
     */
    public function setLogin(?string $login): UserInterface
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
     * @return UserInterface
     */
    public function setPassword(?string $password): UserInterface
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
     * @return UserInterface
     */
    public function setEmail(?string $email): UserInterface
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
        return $this->getAttribute(self::URL);
    }

    /**
     * @param string|null $url
     * @return UserInterface
     */
    public function setUrl(?string $url): UserInterface
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
     * @return UserInterface
     */
    public function setNiceName(?string $niceName): UserInterface
    {
        $this->setAttribute(self::NICE_NAME, $niceName);
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
        // TODO: Implement metas() method.
    }

}