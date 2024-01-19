<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models\Meta;

use Dbout\WpOrm\Attributes\MetaConfigAttribute;
use Dbout\WpOrm\Exceptions\WpOrmException;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait WithMeta
{
    /**
     * @var MetaConfigAttribute
     */
    protected MetaConfigAttribute $metaConfig;

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
        $reflection = new \ReflectionClass(static::class);
        $configs = $reflection->getAttributes(MetaConfigAttribute::class);
        if ($configs === []) {
            throw new WpOrmException(sprintf('Please define attribute %s.', MetaConfigAttribute::class));
        }

        /** @var MetaConfigAttribute $config */
        $config  = $configs[0];
        $this->metaConfig = $config;
    }

    /**
     * @return HasMany
     */
    public function metas(): HasMany
    {
        return $this->hasMany($this->metaConfig->metaClass, $this->metaConfig->foreignKey);
    }

    /**
     * @param string $metaKey
     * @return AbstractMeta|null
     */
    public function getMeta(string $metaKey): ?AbstractMeta
    {
        /** @var ?AbstractMeta $value */
        // @phpstan-ignore-next-line
        $value =  $this->metas()->firstWhere($this->metaConfig->columnKey, $metaKey);
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
            ->where($this->metaConfig->columnKey, $metaKey)
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
                $this->metaConfig->foreignKey => $metaKey,
            ]);

        $instance->fill([
            $this->metaConfig->columnValue => $value,
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
            ->where($this->metaConfig->columnKey, $metaKey)
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
}
