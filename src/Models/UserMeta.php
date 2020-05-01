<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Contracts\MetaInterface;
use Dbout\WpOrm\Contracts\UserInterface;
use Dbout\WpOrm\Contracts\UserMetaInterface;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class UserMeta
 * @package Dbout\WpOrm\Models
 *
 * @method static UserMetaInterface find($metaId);
 * @method static Builder user($userId);
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
class UserMeta extends AbstractModel implements UserMetaInterface
{

    /**
     * @var string
     */
    protected $table = 'usermeta';

    /**
     * @var string
     */
    protected $primaryKey = self::META_ID;

    /**
     * Disable created_at and updated_at
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return HasOne|UserInterface
     */
    public function getUser(): HasOne
    {
        return $this->hasOne(User::class, UserInterface::USER_ID, self::USER_ID);
    }

    /**
     * @param $user
     * @return UserMetaInterface
     */
    public function setUser($user): UserMetaInterface
    {
        $this->setAttribute(self::USER_ID, $user);
        return $this;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->getAttribute(self::META_KEY);
    }

    /**
     * @param string $key
     * @return MetaInterface
     */
    public function setKey(string $key): MetaInterface
    {
        $this->setAttribute(self::META_KEY, $key);
        return $this;
    }

    /**
     * @return mixed|void
     */
    public function getValue()
    {
        return $this->getAttribute(self::META_VALUE);
    }

    /**
     * @param string $value
     * @return MetaInterface
     */
    public function setValue(string $value): MetaInterface
    {
        $this->setAttribute(self::META_VALUE, $value);
        return $this;
    }

    /**
     * @param Builder $builder
     * @param $user
     */
    public function scopeUser(Builder $builder, $user)
    {
        $builder->where(self::USER_ID, $user);
    }

}