<?php
namespace Dbout\WpOrm\Orm;

use Illuminate\Database\ConnectionResolverInterface;

/**
 * Class Resolver
 * @package Dbout\WpOrm\Orm
 */
class Resolver implements ConnectionResolverInterface
{

    /**
     * Get a database connection instance.
     *
     * @param  string $name
     *
     * @return \Illuminate\Database\Connection
     */
    public function connection( $name = null )
    {
        return Database::getInstance();
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection()
    {

    }

    /**
     * Set the default connection name.
     *
     * @param  string $name
     *
     * @return void
     */
    public function setDefaultConnection( $name )
    {

    }
}