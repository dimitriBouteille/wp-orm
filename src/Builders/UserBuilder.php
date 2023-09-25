<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Builders;

use Dbout\WpOrm\Builders\Traits\WithMeta;
use Dbout\WpOrm\Models\User;

class UserBuilder extends AbstractBuilder
{
    use WithMeta;

    /**
     * @param string $email
     * @return User|null
     */
    public function findOneByEmail(string $email): ?User
    {
        return $this->firstWhere(User::EMAIL, $email);
    }

    /**
     * @param string $login
     * @return User|null
     */
    public function findOneByLogin(string $login): ?User
    {
        return $this->firstWhere(User::LOGIN, $login);
    }

    /**
     * @param string $email
     * @return $this
     */
    public function whereEmail(string $email): self
    {
        return $this->where(User::EMAIL, $email);
    }

    /**
     * @param string $login
     * @return $this
     */
    public function whereLogin(string $login): self
    {
        return $this->where(User::LOGIN, $login);
    }

    /**
     * @param mixed ...$emails
     * @return $this
     */
    public function whereEmails(... $emails): self
    {
        return $this->_whereOrIn(User::EMAIL, $emails);
    }

    /**
     * @param mixed ...$logins
     * @return $this
     */
    public function whereLogins(... $logins): self
    {
        return $this->_whereOrIn(User::LOGIN, $logins);
    }
}
