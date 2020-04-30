<?php

namespace Dbout\WpOrm\Orm;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AbstractModel
 * @package Dbout\WpOrm\Orm
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
abstract class AbstractModel extends Model
{

    /**
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * AbstractModel constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        static::$resolver = new Resolver();
        parent::__construct($attributes);
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return Builder
     */
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();
        return new Builder(
            $connection, $connection->getQueryGrammar(), $connection->getPostProcessor()
        );
    }

    /**
     * @return Database|null
     */
    public function getConnection()
    {
        return Database::getInstance();
    }

    /**
     * Add wordpress table prefix
     * @return string
     */
    public function getTable()
    {
        $prefix = $this->getConnection()->db->prefix;
        if (!empty($this->table)) {

            // Ajoute plusieurs fois le suffix, va savoir pourquoi
            // @todo Corriger le bug ci dessus
            return substr($this->table, 0, strlen($prefix)) === $prefix ? $this->table : $prefix . $this->table;
        }

        $table = substr(strrchr(get_class($this), "\\"), 1);
        $table = snake_case(str_plural($table));

        // Add wordpress table prefix
        return $prefix . $table;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->{$this->primaryKey};
    }

}