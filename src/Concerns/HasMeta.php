<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Concerns;

use Dbout\WpOrm\MetaMappingConfig;
use Dbout\WpOrm\Models\Meta\AbstractMeta;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasMeta
{
    /**
     * @var array
     */
    protected array $_tmpMetas = [];

    /**
     * @return void
     */
    protected static function bootHasMeta(): void
    {
        static::saved(function ($model) {
            $model->saveTmpMetas();
        });
    }

    /**
     * @return HasMany
     */
    public function metas(): HasMany
    {
        return $this->hasMany($this->getMetaConfigMapping()->metaClass, $this->getMetaConfigMapping()->foreignKey);
    }

    /**
     * @param string $metaKey
     * @return AbstractMeta|null
     */
    public function getMeta(string $metaKey): ?AbstractMeta
    {
        /** @var ?AbstractMeta $value */
        // @phpstan-ignore-next-line
        $value =  $this->metas()->firstWhere($this->getMetaConfigMapping()->columnKey, $metaKey);
        return $value;
    }

    /**
     * @param string $metaKey
     * @return mixed|null
     */
    public function getMetaValue(string $metaKey): mixed
    {
        if (!$this->exists) {
            return $this->_tmpMetas[$metaKey] ?? null;
        }

        $meta = $this->getMeta($metaKey);
        return $meta?->getValue();

    }

    /**
     * @param string $metaKey
     * @return bool
     */
    public function hasMeta(string $metaKey): bool
    {
        // @phpstan-ignore-next-line
        return $this->metas()
            ->where($this->getMetaConfigMapping()->columnKey, $metaKey)
            ->exists();
    }

    /**
     * @param string $metaKey
     * @param mixed $value
     * @return AbstractMeta|null
     */
    public function setMeta(string $metaKey, mixed $value): ?AbstractMeta
    {
        if (!$this->exists) {
            $this->_tmpMetas[$metaKey] = $value;
            return null;
        }

        /** @var AbstractMeta $instance */
        $instance = $this->metas()
            ->firstOrNew([
                $this->getMetaConfigMapping()->columnKey => $metaKey,
            ]);

        $instance->fill([
            $this->getMetaConfigMapping()->columnValue => $value,
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

        // @phpstan-ignore-next-line
        return $this->metas()
            ->where($this->getMetaConfigMapping()->columnKey, $metaKey)
            ->forceDelete();
    }

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

    /**
     * @return MetaMappingConfig
     */
    abstract public function getMetaConfigMapping(): MetaMappingConfig;
}
