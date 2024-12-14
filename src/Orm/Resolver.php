<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Orm;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\ConnectionResolverInterface;

class Resolver implements ConnectionResolverInterface
{
    /**
     * The default connection name.
     * @var string
     */
    protected string $default;

    /**
     * @inheritDoc
     */
    public function connection($name = null): ConnectionInterface
    {
        return Database::getInstance();
    }

    /**
     * @inheritDoc
     */
    public function getDefaultConnection(): string
    {
        return $this->default ?? '';
    }

    /**
     * @inheritDoc
     */
    public function setDefaultConnection($name): void
    {
        $this->default = $name;
    }
}
