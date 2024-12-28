<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Orm;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static int upsert(array $values, array|string $uniqueBy, array|null $update = null) Insert new records or update the existing ones.
 * @method static static|null find(int|string $objectId) Retrieve a model by its primary key.
 * @method static void truncate() Delete all the model's associated database records, operation will also reset any auto-incrementing IDs on the model's associated table.
 */
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
    public function getTable()
    {
        $prefix = $this->getConnection()->getTablePrefix();
        if ($this->table !== null && $this->table !== '') {
            return str_starts_with($this->table, $prefix) ? $this->table : $prefix . $this->table;
        }

        // Add WordPress table prefix
        return $prefix . parent::getTable();
    }

    /**
     * @inheritDoc
     */
    protected function newBaseQueryBuilder(): Builder
    {
        $connection = $this->getConnection();
        return new Builder(
            $connection,
            $connection->getQueryGrammar(),
            $connection->getPostProcessor()
        );
    }

    /**
     * @return int|string|null
     */
    public function getId(): null|int|string
    {
        return $this->{$this->primaryKey};
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
