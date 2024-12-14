<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Concerns;

use Dbout\WpOrm\MetaMappingConfig;
use Dbout\WpOrm\Models\Meta\AbstractMeta;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection as BaseCollection;

trait HasMetas
{
    /**
     * @var array
     */
    protected array $_tmpMetas = [];

    /**
     * The metas that should be cast.
     *
     * @var array<string, mixed>
     */
    protected array $metaCasts = [];

    /**
     * The built-in, primitive cast types supported by Eloquent.
     *
     * @var string[]
     */
    protected static array $primitiveMetaCastTypes = [
        'array',
        'bool',
        'boolean',
        'collection',
        'date',
        'datetime',
        'double',
        'float',
        'immutable_date',
        'int',
        'integer',
        'json',
        'object',
        'string',
        'timestamp',
    ];

    /**
     * The cache of the converted meta cast types.
     *
     * @var array
     */
    protected static array $metaCastTypeCache = [];

    /**
     * Initialize the trait.
     *
     * @return void
     */
    protected function initializeHasMetas(): void
    {
        $this->metaCasts = $this->ensureCastsAreStringValues(
            array_merge($this->metaCasts, $this->metaCasts()),
        );
    }

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
        /** @var AbstractMeta $value */
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
        if (!$meta instanceof AbstractMeta) {
            return $meta;
        }

        $value = $meta->getValue();
        if (!$this->metaHasCast($metaKey)) {
            return $value;
        }

        // If the meta exists within the cast array, we will convert it to
        // an appropriate native PHP type dependent upon the associated value
        // given with the key in the pair. Dayle made this comment line up.
        return $this->castMeta($metaKey, $value);
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

    /**
     * Get the metas that should be cast.
     *
     * @return array
     */
    public function getMetaCasts(): array
    {
        return $this->metaCasts;
    }

    /**
     * Get the metas that should be cast.
     *
     * @return array
     */
    protected function metaCasts(): array
    {
        return [];
    }

    /**
     * Cast a meta to a native PHP type.
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    protected function castMeta(string $key, mixed $value): mixed
    {
        $castType = $this->getMetaCastType($key);
        if (is_null($value) && in_array($castType, static::$primitiveMetaCastTypes, true)) {
            return null;
        }

        switch ($castType) {
            case 'int':
            case 'integer':
                return (int)$value;
            case 'real':
            case 'float':
            case 'double':
                return (float)$value;
            case 'string':
                return (string)$value;
            case 'bool':
            case 'boolean':
                return (bool) $value;
            case 'array':
            case 'json':
                return $this->fromJson($value);
            case 'object':
                return $this->fromJson($value, true);
            case 'collection':
                return new BaseCollection($this->fromJson($value));
            case 'date':
                return $this->asDate($value);
            case 'datetime':
                return $this->asDateTime($value);
            case 'immutable_date':
                return $this->asDate($value)->toImmutable();
            case 'timestamp':
                return $this->asTimestamp($value);
        }

        if ($this->isEnumMetaCastable($key)) {
            return $this->getEnumCastableMetaValue($key, $value);
        }

        /**
         * @todo Support custom class cast
         */

        return $value;
    }

    /**
     * Determine if the given key is cast using an enum.
     *
     * @param string $key
     * @return bool
     */
    protected function isEnumMetaCastable(string $key): bool
    {
        $casts = $this->getMetaCasts();
        if (!array_key_exists($key, $casts)) {
            return false;
        }

        $castType = $casts[$key];
        if (in_array($castType, static::$primitiveMetaCastTypes, true)) {
            return false;
        }

        return enum_exists($castType);
    }

    /**
     * Cast the given meta to an enum.
     *
     * @param string $key
     * @param mixed $value
     * @return \UnitEnum|\BackedEnum|null
     */
    protected function getEnumCastableMetaValue(string $key, mixed $value): null|\UnitEnum|\BackedEnum
    {
        if (is_null($value)) {
            return null;
        }

        $castType = $this->getMetaCasts()[$key];
        if ($value instanceof $castType) {
            return $value;
        }

        return $this->getEnumCaseFromValue($castType, $value);
    }

    /**
     * Determine whether a meta should be cast to a native type.
     *
     * @param string $key
     * @param string|null $types
     * @return bool
     */
    public function metaHasCast(string $key, string $types = null): bool
    {
        if (array_key_exists($key, $this->getMetaCasts())) {
            return !$types || in_array($this->getMetaCastType($key), (array)$types, true);
        }

        return false;
    }

    /**
     * Get the type of cast for a meta.
     *
     * @param string $key
     * @return string
     */
    protected function getMetaCastType(string $key): string
    {
        $castType = $this->getMetaCasts()[$key] ?? null;
        if (isset(static::$metaCastTypeCache[$castType])) {
            return static::$metaCastTypeCache[$castType];
        }

        if ($this->isCustomDateTimeCast($castType)) {
            $convertedCastType = 'custom_datetime';
        } elseif ($this->isImmutableCustomDateTimeCast($castType)) {
            $convertedCastType = 'immutable_custom_datetime';
        } elseif ($this->isDecimalCast($castType)) {
            $convertedCastType = 'decimal';
        } elseif (class_exists($castType)) {
            $convertedCastType = $castType;
        } else {
            $convertedCastType = trim(strtolower((string)$castType));
        }

        return static::$metaCastTypeCache[$castType] = $convertedCastType;
    }
}
