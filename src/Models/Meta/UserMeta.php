<?php

namespace Dbout\WpOrm\Models\Meta;

use Dbout\WpOrm\Models\User;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class UserMeta
 * @package Dbout\WpOrm\Models\Meta
 *
 * @method static UserMeta find(int $metaId);
 * @property User|null $user
 */
class UserMeta extends AbstractMeta
{

    const META_ID = 'umeta_id';
    const USER_ID = 'user_id';

    /**
     * @var string
     */
    protected $primaryKey = self::META_ID;

    /**
     * @var string
     */
    protected $table = 'usermeta';

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, User::USER_ID, self::USER_ID);
    }
}
