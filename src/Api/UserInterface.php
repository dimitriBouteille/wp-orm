<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Api;

/**
 * @since 3.0.0
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
