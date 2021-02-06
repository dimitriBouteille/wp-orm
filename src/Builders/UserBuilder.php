<?php

namespace Dbout\WpOrm\Builders;

use Dbout\WpOrm\Models\User;

/**
 * Class UserBuilder
 * @package Dbout\WpOrm\Builders
 */
class UserBuilder extends AbstractBuilder
{

    /**
     * @param mixed ...$emails
     * @return $this
     */
    public function emails(... $emails): self
    {
        return $this->_whereOrIn(User::EMAIL, $emails);
    }

    /**
     * @param mixed ...$logins
     * @return $this
     */
    public function logins(... $logins): self
    {
        return $this->_whereOrIn(User::LOGIN, $logins);
    }
}