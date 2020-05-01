<?php
namespace Dbout\WpOrm\Orm;

use Illuminate\Database\ConnectionResolverInterface;

/**
 * Class Resolver
 * @package Dbout\WpOrm\Orm
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
class Resolver implements ConnectionResolverInterface
{

    /**
     * @param null $name
     * @return Database|\Illuminate\Database\ConnectionInterface|null
     */
    public function connection($name = null)
    {
        return Database::getInstance();
    }

    /**
     * @return string|void
     */
    public function getDefaultConnection()
    {

    }

    /**
     * @param string $name
     */
    public function setDefaultConnection( $name )
    {

    }
}