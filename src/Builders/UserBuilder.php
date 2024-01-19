<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Builders;

use Dbout\WpOrm\Api\UserInterface;
use Dbout\WpOrm\Models\User;

class UserBuilder extends AbstractWithMetaBuilder
{
    /**
     * @param string $email
     * @return User|null
     * @deprecated Remove in next version
     * @see User::findOneByEmail()
     */
    public function findOneByEmail(string $email): ?User
    {
        /** @var User|null $model */
        $model = $this->firstWhere(UserInterface::EMAIL, $email);
        return $model;
    }

    /**
     * @param string $login
     * @return User|null
     * @deprecated Remove in next version
     * @see User::findOneByLogin()
     */
    public function findOneByLogin(string $login): ?User
    {
        /** @var User|null $model */
        $model = $this->firstWhere(UserInterface::LOGIN, $login);
        return $model;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function whereEmail(string $email): self
    {
        return $this->where(UserInterface::EMAIL, $email);
    }

    /**
     * @param string $login
     * @return $this
     */
    public function whereLogin(string $login): self
    {
        return $this->where(UserInterface::LOGIN, $login);
    }

    /**
     * @param mixed ...$emails
     * @return $this
     */
    public function whereEmails(... $emails): self
    {
        return $this->_whereOrIn(UserInterface::EMAIL, $emails);
    }

    /**
     * @param mixed ...$logins
     * @return $this
     */
    public function whereLogins(... $logins): self
    {
        return $this->_whereOrIn(UserInterface::LOGIN, $logins);
    }
}
