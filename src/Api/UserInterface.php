<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Api;

use Carbon\Carbon;

/**
 * @method string|null getUserLogin()
 * @method self setUserLogin(string $login)
 * @method string|null getUserPass()
 * @method self setUserPass(string $password)
 * @method string|null getUserNicename()
 * @method self setUserNicename(string $nicename)
 * @method string|null getUserEmail()
 * @method self setUserEmail(string $email)
 * @method string|null getUserUrl()
 * @method self setUserUrl(?string $url)
 * @method Carbon|null getUserRegistered()
 * @method self setUserRegistered($date)
 * @method string|null getUserActivationKey()
 * @method self setUserActivationKey(?string $key)
 * @method int getUserStatus()
 * @method self setUserStatus(int $status)
 * @method string|null getDisplayName()
 * @method self setDisplayName(?string $name)
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
    public const CREATED_AT = 'user_registered';
    public const UPDATED_AT = null;

}
