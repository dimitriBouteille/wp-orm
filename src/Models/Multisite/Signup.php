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
 * @method string getDomain()
 * @method Signup setDomain(string $domain)
 * @method string getPath()
 * @method Signup setPath(string $path)
 * @method string getTitle()
 * @method Signup setTitle(string $title)
 * @method string getUserLogin()
 * @method Signup setUserLogin(string $userLogin)
 * @method string getUserEmail()
 * @method Signup setUserEmail(string $userEmail)
 * @method Carbon getRegistered()
 * @method Signup setRegistered($registered)
 * @method Carbon getActivated()
 * @method Signup setActivated($activated)
 * @method bool getActive()
 * @method Signup setActive(bool $active)
 * @method string getActivationKey()
 * @method Signup setActivationKey(string $activationKey)
 * @method string|null getMeta()
 * @method Signup setMeta(?string $meta)
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
