<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models\Multisite;

use Carbon\Carbon;
use Dbout\WpOrm\Models\User;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read int $signup_id
 * @property string $domain
 * @property string $path
 * @property string $title
 * @property string $user_login
 * @property string $user_email
 * @property Carbon $registered
 * @property Carbon $activated
 * @property bool $active
 * @property string $activation_key
 * @property string|null $meta
 *
 * @property-read User|null $user
 */
class Signup extends AbstractModel
{
    public const CREATED_AT = self::REGISTERED;
    public const UPDATED_AT = null;
    final public const SIGNUP_ID = 'signup_id';
    final public const DOMAIN = 'domain';
    final public const PATH = 'path';
    final public const TITLE = 'title';
    final public const USER_LOGIN = 'user_login';
    final public const USER_EMAIL = 'user_email';
    final public const REGISTERED = 'registered';
    final public const ACTIVATED = 'activated';
    final public const ACTIVE = 'active';
    final public const ACTIVATION_KEY = 'activation_key';
    final public const META = 'meta';

    protected bool $useBasePrefix = true;

    protected $table = 'signups';

    protected $primaryKey = self::SIGNUP_ID;

    protected $casts = [
        self::REGISTERED => 'datetime',
        self::ACTIVATED => 'datetime',
        self::ACTIVE => 'bool',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, User::EMAIL, self::USER_EMAIL);
    }
}
