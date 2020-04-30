<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Contracts\UserInterface;
use Dbout\WpOrm\Contracts\UserMetaInterface;

/**
 * Class UserMeta
 * @package Dbout\WpOrm\Models
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
class UserMeta extends AbstractMeta implements UserMetaInterface
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
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
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

}