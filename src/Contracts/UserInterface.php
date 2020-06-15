<?php

namespace Dbout\WpOrm\Contracts;

use \Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Interface UserInterface
 * @package Dbout\WpOrm\Contracts
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
interface UserInterface
{

    const USER_ID = 'ID';
    const USER_LOGIN = 'user_login';
    const USER_PASS = 'user_pass';
    const USER_NICENAME = 'user_nicename';
    const USER_EMAIL = 'user_email';
    const USER_URL = 'user_url';
    const USER_REGISTERED = 'user_registered';
    const USER_ACTIVATION_KEY = 'user_activation_key';
    const DISPLAY_NAME = 'display_name';

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return string|null
     */
    public function getLogin(): ?string;

    /**
     * @param string|null $login
     * @return UserInterface
     */
    public function setLogin(?string $login): UserInterface;

    /**
     * @return string|null
     */
    public function getPassword(): ?string;

    /**
     * @param string|null $password
     * @return UserInterface
     */
    public function setPassword(?string $password): UserInterface;

    /**
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * @param string|null $email
     * @return UserInterface
     */
    public function setEmail(?string $email): UserInterface;

    /**
     * @return string|null
     */
    public function getDisplayName(): ?string;

    /**
     * @param string|null $displayName
     * @return UserInterface
     */
    public function setDisplayName(?string $displayName): UserInterface;

    /**
     * @return string|null
     */
    public function getUrl(): ?string;

    /**
     * @param string|null $url
     * @return UserInterface
     */
    public function setUrl(?string $url): UserInterface;

    /**
     * @return string|null
     */
    public function getNiceName(): ?string;

    /**
     * @param string|null $niceName
     * @return UserInterface
     */
    public function setNiceName(?string $niceName): UserInterface;

    /**
     * @return \DateTimeInterface|null
     */
    public function getRegistered(): ?\DateTimeInterface;

    /**
     * @param $registered
     * @return UserInterface
     */
    public function setRegistered($registered): UserInterface;

    /**
     * @return string|null
     */
    public function getActivationKey(): ?string;

    /**
     * @param string|null $activationKey
     * @return UserInterface
     */
    public function setActivationKey(?string $activationKey): UserInterface;

    /**
     * @return HasMany
     */
    public function comments(): HasMany;

    /**
     * @return HasMany
     */
    public function metas(): HasMany;

    /**
     * @return HasMany
     */
    public function posts(): HasMany;
}
