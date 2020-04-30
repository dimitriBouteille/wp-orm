<?php

namespace Dbout\WpOrm\Contracts;

/**
 * Interface UserMetaInterface
 * @package Dbout\WpOrm\Contracts
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
interface UserMetaInterface extends MetaInterface
{

    const META_ID = 'umeta_id';
    const USER_ID = 'user_id';

    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface;

    /**
     * @param $user
     * @return UserMetaInterface
     */
    public function setUser($user): UserMetaInterface;

}