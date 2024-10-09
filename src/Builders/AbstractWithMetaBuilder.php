<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Builders;

use Dbout\WpOrm\Api\WithMetaModelInterface;
use Dbout\WpOrm\Exceptions\MetaNotSupportedException;
use Dbout\WpOrm\Exceptions\WpOrmException;
use Dbout\WpOrm\MetaMappingConfig;
use Dbout\WpOrm\Orm\AbstractModel;
use Dbout\WpOrm\Orm\Database;
use Illuminate\Database\Eloquent\Model;

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
     * @var MetaMappingConfig|null
     */
    protected ?MetaMappingConfig $metaConfig = null;

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
            $alias = sprintf('%s_value', $metaKey);
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
                Database::getInstance()->raw(sprintf("'%s'", $metaKey))
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
        if (!$this->model instanceof WithMetaModelInterface) {
            throw new MetaNotSupportedException(sprintf(
                'Model %s must be implement %s',
                get_class($this->model),
                WithMetaModelInterface::class
            ));
        }

        $config = $this->model->getMetaConfigMapping();
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
