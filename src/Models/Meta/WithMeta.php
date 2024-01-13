<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models\Meta;

use Dbout\WpOrm\Exceptions\MetaNotSupportedException;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait WithMeta
{
    /**
     * @var AbstractMeta|null
     */
    protected ?AbstractMeta $metaModel = null;

    /**
     * @var array
     */
    protected array $_tmpMetas = [];

    /**
     * @return void
     */
    protected static function bootWithMeta()
    {
        static::saved(function ($model) {
            $model->saveTmpMetas();
        });
    }

    /**
     * @throws MetaNotSupportedException
     * @throws \ReflectionException
     */
    public function initializeWithMeta(): void
    {
        $metaClass = $this->getMetaClass();
        $object = (new \ReflectionClass($metaClass));
        if (!$object->implementsInterface(MetaInterface::class)) {
            throw new MetaNotSupportedException(sprintf(
                "Model %s must be implement %s",
                $metaClass,
                MetaInterface::class
            ));
        }

        $this->metaModel = $object->newInstanceWithoutConstructor();
    }

    /**
     * @return HasMany
     */
    public function metas(): HasMany
    {
        return $this->hasMany(get_class($this->metaModel), $this->metaModel->getFkColumn());
    }

    /**
     * @param string $metaKey
     * @return AbstractMeta|null
     */
    public function getMeta(string $metaKey): ?AbstractMeta
    {
        return $this->metas()
            ->firstWhere($this->metaModel->getKeyColumn(), $metaKey);
    }

    /**
     * @param string $metaKey
     * @return mixed|null
     */
    public function getMetaValue(string $metaKey)
    {
        if (!$this->exists) {
            return $this->_tmpMetas[$metaKey] ?? null;
        }

        $meta = $this->getMeta($metaKey);
        if (!$meta) {
            return null;
        }

        return $meta->getValue();
    }

    /**
     * @param string $metaKey
     * @return bool
     */
    public function hasMeta(string $metaKey): bool
    {
        return $this->metas()
            ->where($this->metaModel->getKeyColumn(), $metaKey)
            ->exists();
    }

    /**
     * @param string $metaKey
     * @param $value
     * @return AbstractMeta|null
     */
    public function setMeta(string $metaKey, $value): ?AbstractMeta
    {
        if (!$this->exists) {
            $this->_tmpMetas[$metaKey] = $value;
            return null;
        }

        $instance = $this->metas()
            ->firstOrNew([
                $this->metaModel->getKeyColumn() => $metaKey,
            ]);

        $instance->fill([
            $this->metaModel->getValueColumn() => $value,
        ])->save();

        return $instance;
    }

    /**
     * @param string $metaKey
     * @return bool
     */
    public function deleteMeta(string $metaKey): bool
    {
        if (!$this->exists) {
            unset($this->_tmpMetas[$metaKey]);
            return true;
        }

        return $this->metas()
            ->where($this->metaModel->getKeyColumn(), $metaKey)
            ->forceDelete();
    }

    /**
     * @return string
     */
    abstract public function getMetaClass(): string;

    /**
     * @return void
     */
    protected function saveTmpMetas(): void
    {
        foreach ($this->_tmpMetas as $metaKey => $value) {
            $this->setMeta($metaKey, $value);
        }

        $this->_tmpMetas = [];
    }
}
