<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Models\Meta;

use Dbout\WpOrm\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read User|null $user
 */
class UserMeta extends AbstractMeta
{
    final public const META_ID = 'umeta_id';
    final public const USER_ID = 'user_id';

    /**
     * @var string
     */
    protected $primaryKey = self::META_ID;

    /**
     * @var string
     */
    protected $table = 'usermeta';

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::USER_ID, User::USER_ID);
    }
}
