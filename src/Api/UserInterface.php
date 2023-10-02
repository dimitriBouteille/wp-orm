<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Api;

use Carbon\Carbon;
use Dbout\WpOrm\Models\User;

/**
 * @method string|null getUserLogin()
 * @method User setUserLogin(string $login)
 * @method string|null getUserPass()
 * @method User setUserPass(string $password)
 * @method string|null getUserNicename()
 * @method User setUserNicename(string $nicename)
 * @method string|null getUserEmail()
 * @method User setUserEmail(string $email)
 * @method string|null getUserUrl()
 * @method User setUserUrl(?string $url)
 * @method Carbon|null getUserRegistered()
 * @method User setUserRegistered($date)
 * @method string|null getUserActivationKey()
 * @method User setUserActivationKey(?string $key)
 * @method int getUserStatus()
 * @method User setUserStatus(int $status)
 * @method string|null getDisplayName()
 * @method User setDisplayName(?string $name)
 */
interface UserInterface
{
    public const USER_ID = 'ID';
    public const LOGIN = 'user_login';
    public const PASSWORD = 'user_pass';
    public const NICE_NAME = 'user_nicename';
    public const EMAIL = 'user_email';
    public const URL = 'user_url';
    public const REGISTERED = 'user_registered';
    public const ACTIVATION_KEY = 'user_activation_key';
    public const DISPLAY_NAME = 'display_name';
    public const STATUS = 'user_status';
}
