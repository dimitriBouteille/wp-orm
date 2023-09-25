<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Orm;

use Illuminate\Database\ConnectionResolverInterface;

class Resolver implements ConnectionResolverInterface
{
    /**
     * @inheritDoc
     */
    public function connection($name = null)
    {
        return Database::getInstance();
    }

    /**
     * @inheritDoc
     */
    public function getDefaultConnection()
    {
    }

    /**
     * @inheritDoc
     */
    public function setDefaultConnection($name)
    {
    }
}
