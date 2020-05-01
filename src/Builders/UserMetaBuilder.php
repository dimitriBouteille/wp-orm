<?php

namespace Dbout\WpOrm\Builders;

use Dbout\WpOrm\Contracts\UserMetaInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class UserMetaBuilder
 * @package Dbout\WpOrm\Builders
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
class UserMetaBuilder extends Builder
{

    /**
     * @param $user
     * @return \Illuminate\Database\Query\Builder
     */
    public function whereUser($user)
    {
        return $this->query
            ->where(UserMetaInterface::USER_ID, $user);
    }

}