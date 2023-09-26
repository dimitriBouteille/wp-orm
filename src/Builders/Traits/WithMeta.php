<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Builders\Traits;

use Dbout\WpOrm\Exceptions\MetaNotSupportedException;
use Dbout\WpOrm\Models\Meta\AbstractMeta;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Model;

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
    protected ?AbstractMeta $metaModel = null;

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

        $column = sprintf('%s.%s AS %s', $metaKey, $this->metaModel->getValueColumn(), $alias);
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

            $this->addMetaToSelect($metaName, $alias);
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
            ->joinToMeta($metaKey)
            ->where(sprintf('%s.%s', $metaKey, $this->metaModel->getValueColumn()), $operator, $value);

        return $this;
    }

    /**
     * @inheritDoc
     * @throws MetaNotSupportedException
     * @throws \ReflectionException
     */
    public function setModel(Model $model)
    {
        $traits = class_uses_recursive(get_class($model));
        if (!in_array(\Dbout\WpOrm\Models\Meta\WithMeta::class, $traits, true)) {
            throw new MetaNotSupportedException(sprintf(
                'Model %s must be use trait %s',
                get_class($model),
                \Dbout\WpOrm\Models\Meta\WithMeta::class
            ));
        }

        /** @var \Dbout\WpOrm\Models\Meta\WithMeta $model */
        $metaClass = $model->getMetaClass();
        $object = (new \ReflectionClass($metaClass));
        $this->metaModel = $object->newInstanceWithoutConstructor();

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
        $joinTable = sprintf('%s AS %s', $this->metaModel->getTable(), $metaKey);

        if ($this->joined($this, $joinTable)) {
            return $this;
        }

        $join = $this->joinCallback[$joinType];
        $this->$join($joinTable, function ($join) use ($metaKey, $model) {
            /** @var \Illuminate\Database\Query\JoinClause $join */
            $join->on(
                sprintf('%s.%s', $metaKey, $this->metaModel->getKeyColumn()),
                '=',
                $join->raw(sprintf("'%s'", $metaKey))
            )->on(
                $join->raw(sprintf('%s.%s', $metaKey, $this->metaModel->getFkColumn())),
                '=',
                sprintf('%s.%s', $model->getTable(), $model->getKeyName()),
            );
        });

        return $this;
    }
}
