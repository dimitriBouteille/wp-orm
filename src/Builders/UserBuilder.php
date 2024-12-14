<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Builders;

use Dbout\WpOrm\Models\User;

class UserBuilder extends AbstractWithMetaBuilder
{
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
