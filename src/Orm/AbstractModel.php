<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
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
     * @inheritDoc
     */
    protected $guarded = [];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        static::$resolver = new Resolver();
        parent::__construct($attributes);
    }

    /**
     * @inheritDoc
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
        // @phpstan-ignore-next-line
        return DatabaseV2::getInstance();
    }

    /**
     * @inheritDoc
     */
    public function getTable(): string
    {
        $prefix = $this->getConnection()->getTablePrefix();

        if (!empty($this->table)) {
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
     * @deprecated Remove in next version
     * @see self::getTable()
     * @see https://stackoverflow.com/a/20812314
     */
    public static function table(): string
    {
        // @phpstan-ignore-next-line
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
        $attribute = strtolower((string)preg_replace('/(?<!^)[A-Z]/', '_$0', $attribute));

        if ($type === 'get') {
            return $this->getAttribute($attribute);
        }

        $this->setAttribute($attribute, ...$parameters);
        return $this;
    }
}
