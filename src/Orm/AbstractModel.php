<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Orm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class AbstractModel extends Model
{
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
     * @return Builder|\Illuminate\Database\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();
        return new Builder(
            $connection,
            $connection->getQueryGrammar(),
            $connection->getPostProcessor()
        );
    }

    /**
     * @inheritDoc
     */
    public function getConnection()
    {
        return Database::getInstance();
    }

    /**
     * Get table name associated with the model
     * Add WordPress table prefix
     *
     * @return string
     */
    public function getTable(): string
    {
        $prefix = $this->getConnection()->getTablePrefix();

        if (!empty($this->table)) {
            // Ajoute plusieurs fois le suffix, va savoir pourquoi ...
            // @todo Corriger le bug ci dessus
            return str_starts_with($this->table, $prefix) ? $this->table : $prefix . $this->table;
        }

        $table = \substr(\strrchr(\get_class($this), "\\"), 1);
        $table = Str::snake(Str::plural($table));

        // Add WordPress table prefix
        return $prefix . $table;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->{$this->primaryKey};
    }

    /**
     * Returns model table name
     *
     * @return string
     */
    public static function table(): string
    {
        return (new static())->getTable();
    }

    /**
     * @inheritDoc
     */
    public function __call($method, $parameters)
    {
        preg_match('#^(get|set)(.*)#', $method, $matchGetter);
        if ($matchGetter === []) {
            return parent::__call($method, $parameters);
        }

        $type = $matchGetter[1];
        $attribute = $matchGetter[2];
        $attribute = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $attribute));

        if ($type === 'get') {
            return $this->getAttribute($attribute);
        }

        $this->setAttribute($attribute, ...$parameters);
        return $this;
    }
}
