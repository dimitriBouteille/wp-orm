<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Contracts\UserInterface;
use Dbout\WpOrm\Contracts\UserMetaInterface;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class UserMeta
 * @package Dbout\WpOrm\Models
 *
 * @method static UserMetaInterface find($metaId);
 * @property UserInterface|null $user
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
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, UserInterface::USER_ID, self::USER_ID);
    }

}