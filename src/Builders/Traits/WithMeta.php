<?php

namespace Dbout\WpOrm\Builders\Traits;

use Dbout\WpOrm\Models\Meta\AbstractMeta;
use Dbout\WpOrm\Models\Meta\MetaMap;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait WithMeta
 * @package Dbout\WpOrm\Builders\Traits
 */
trait WithMeta
{

    /**
     * @var array|string[]
     */
    protected array $joinCallback = [
        'inner' => 'join',
        'left' => 'leftJoin',
        'right' => 'rightJoin',
    ];

    /**
     * @var AbstractMeta|null
     */
    protected ?AbstractMeta $modelMeta = null;

    /**
     * @var MetaMap|null
     */
    protected ?MetaMap $metaMap = null;

    /**
     * @param string $metaKey
     * @param string|null $alias
     * @return $this
     */
    public function addMetaToSelect(string $metaKey, ?string $alias = null): self
    {
        $this
            ->joinToMeta($metaKey);

        if (!$alias) {
            $alias = $metaKey;
        }

        $column = sprintf('%s.%s AS %s', $metaKey, $this->modelMeta->getValueColumn(), $alias);
        $this->addSelect($column);
        return $this;
    }

    /**
     * @param array $metas
     * @return $this
     */
    public function addMetasToSelect(array $metas): self
    {
        foreach ($metas as $key => $metaName) {
            $alias = null;
            if (is_string($key)) {
                $alias = $key;
            }

            $this->addMetaToSelect($metaName, $key);
        }
        
        return $this;
    }

    /**
     * @param string $metaKey
     * @param $value
     * @param string $operator
     * @return $this
     */
    public function addMetaToFilter(string $metaKey, $value, string $operator = '='): self
    {
        $this
            ->joinToMeta($metaKey,'inner')
            ->where(sprintf('%s.%s', $metaKey, $this->modelMeta->getValueColumn()), $operator, $value);

        return $this;
    }

    /**
     * @param Model $model
     * @return $this
     * @throws \ReflectionException
     */
    public function setModel(Model $model): self
    {
        $this->metaMap = $model->getMetaMap();
        $reflection = (new \ReflectionClass($this->metaMap->getClass()));
        $this->modelMeta = $reflection->newInstanceWithoutConstructor();

        return parent::setModel($model);
    }
    
    /**
     * @param string $metaKey
     * @param string $joinType
     * @return $this
     */
    public function joinToMeta(string $metaKey, string $joinType = 'inner'): self
    {
        /** @var AbstractModel $model */
        $model = $this->model;
        $joinTable = sprintf('%s AS %s', $this->modelMeta->getTable(), $metaKey);

        if ($this->joined($this, $joinTable)) {
            return $this;
        }

        $join = $this->joinCallback[$joinType];
        $this->$join($joinTable, function ($join) use ($metaKey, $model) {
            /** @var \Illuminate\Database\Query\JoinClause $join */
            $join->on(
                sprintf('%s.%s', $metaKey, $this->modelMeta->getKeyColumn()),
            '=',
                $join->raw(sprintf("'%s'", $metaKey))
            )->on(
                $join->raw(sprintf("%s.%s", $metaKey, $this->metaMap->getFk())),
            '=',
                sprintf('%s.%s', $model->getTable(), $model->getKeyName()),
            );
        });

        return $this;
    }
}
