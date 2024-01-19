<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Builders;

use Dbout\WpOrm\Attributes\MetaConfigAttribute;
use Dbout\WpOrm\Exceptions\MetaNotSupportedException;
use Dbout\WpOrm\Exceptions\WpOrmException;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @since 3.0.0
 */
abstract class AbstractWithMetaBuilder extends AbstractBuilder
{
    /**
     * @var array<string, string>
     */
    protected array $joinCallback = [
        'inner' => 'join',
        'left' => 'leftJoin',
        'right' => 'rightJoin',
    ];

    /**
     * @var MetaConfigAttribute|null
     */
    protected ?MetaConfigAttribute $metaConfig = null;

    /**
     * @var string|null
     */
    protected ?string $metaTable = null;

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function setModel(Model $model): self
    {
        parent::setModel($model);
        $this->initMeta();
        return $this;
    }

    /**
     * @param string $metaKey
     * @param string|null $alias
     * @throws WpOrmException
     * @return $this
     */
    public function addMetaToSelect(string $metaKey, ?string $alias = null): self
    {
        $this->joinToMeta($metaKey);
        if ($alias === null || $alias === '') {
            $alias = $metaKey;
        }

        $column = sprintf('%s.%s AS %s', $metaKey, $this->metaConfig?->columnValue, $alias);
        $this->addSelect($column);
        return $this;
    }

    /**
     * @param array<string>|array<string, string> $metas
     * @throws WpOrmException
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
     * @param mixed $value
     * @param string $operator
     * @throws WpOrmException
     * @return $this
     */
    public function addMetaToFilter(string $metaKey, mixed $value, string $operator = '='): self
    {
        $this
            ->joinToMeta($metaKey)
            ->where(sprintf('%s.%s', $metaKey, $this->metaConfig->columnValue), $operator, $value);

        return $this;
    }

    /**
     * @param string $metaKey
     * @param string $joinType
     * @throws WpOrmException
     * @return $this
     */
    public function joinToMeta(string $metaKey, string $joinType = 'inner'): self
    {
        $model = $this->model;
        $joinTable = sprintf('%s AS %s', $this->metaTable, $metaKey);

        if ($this->joined($this, $joinTable)) {
            return $this;
        }

        $join = $this->joinCallback[$joinType] ?? null;
        if ($join === null) {
            throw new WpOrmException('Invalid join type.');
        }

        $this->$join($joinTable, function ($join) use ($metaKey, $model) {
            /** @var \Illuminate\Database\Query\JoinClause $join */
            $join->on(
                sprintf('%s.%s', $metaKey, $this->metaConfig?->columnKey),
                '=',
                $join->raw(sprintf("'%s'", $metaKey))
            )->on(
                sprintf('%s.%s', $metaKey, $this->metaConfig?->foreignKey),
                '=',
                sprintf('%s.%s', $model->getTable(), $model->getKeyName()),
            );
        });

        return $this;
    }

    /**
     * @throws \ReflectionException
     * @throws MetaNotSupportedException
     * @throws WpOrmException
     */
    protected function initMeta(): void
    {
        $traits = class_uses_recursive(get_class($this->model));
        if (!in_array(\Dbout\WpOrm\Concerns\HasMeta::class, $traits, true)) {
            throw new MetaNotSupportedException(sprintf(
                'Model %s must be use trait %s',
                get_class($this->model),
                \Dbout\WpOrm\Concerns\HasMeta::class
            ));
        }

        // @phpstan-ignore-next-line
        $config = $this->model->getMetaConfig();
        $reflection = new \ReflectionClass($config->metaClass);
        if (!$reflection->isSubclassOf(AbstractModel::class)) {
            throw new WpOrmException(sprintf(
                'Class %s must extend from %s.',
                $config->metaClass,
                AbstractModel::class
            ));
        }

        /** @var AbstractModel $metaModel */
        $metaModel = $reflection->newInstanceWithoutConstructor();

        $this->metaTable = $metaModel->getTable();
        $this->metaConfig = $config;
    }
}
