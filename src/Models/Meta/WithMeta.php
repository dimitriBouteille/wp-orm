<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models\Meta;

use Dbout\WpOrm\Exceptions\WpOrmException;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait WithMeta
{
    protected array $metaConfig = [
        'class' => '',
        'columnKey' => '',
        'columnValue' => '',
        'foreignKey' => '',
    ];

    /**
     * @var array
     */
    protected array $_tmpMetas = [];

    /**
     * @return void
     */
    protected static function bootWithMeta(): void
    {
        static::saved(function ($model) {
            $model->saveTmpMetas();
        });
    }

    /**
     * @throws WpOrmException
     * @return void
     */
    public function initializeWithMeta(): void
    {
        foreach ($this->metaConfig as $optionKey => $optionValue) {
            if ($optionValue === null || $optionValue === '') {
                throw new WpOrmException(sprintf('Please define %s key in metaConfig property.', $optionKey));
            }
        }
    }

    /**
     * @return HasMany
     */
    public function metas(): HasMany
    {
        return $this->hasMany($this->metaConfig['class'], $this->getMetaForeignKey());
    }

    /**
     * @param string $metaKey
     * @return AbstractMeta|null
     */
    public function getMeta(string $metaKey): ?AbstractMeta
    {
        /** @var ?AbstractMeta $value */
        $value =  $this->metas()->firstWhere($this->getMetaColumnKey(), $metaKey);
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
        return $this->metas()
            ->where($this->getMetaColumnKey(), $metaKey)
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
                $this->getMetaForeignKey() => $metaKey,
            ]);

        $instance->fill([
            $this->getMetaColumnValue() => $value,
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
            ->where($this->getMetaColumnKey(), $metaKey)
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
     * @return string
     */
    protected function getMetaColumnKey(): string
    {
        return $this->metaConfig['columnKey'] ?? '';
    }

    /**
     * @return string
     */
    protected function getMetaColumnValue(): string
    {
        return $this->metaConfig['columnValue'] ?? '';
    }

    /**
     * @return string
     */
    protected function getMetaForeignKey(): string
    {
        return $this->metaConfig['foreignKey'] ?? '';
    }
}
