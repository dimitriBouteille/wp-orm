<?php

namespace Dbout\WpOrm\Models\Meta;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Trait ModelWithMetas
 * @package Dbout\WpOrm\Models\Meta
 */
trait ModelWithMetas
{

    /**
     * @return HasMany
     */
    public function metas(): HasMany
    {
        return $this->hasMany($this->_getMetaClass(), $this->_getMetaFk());
    }

    /**
     * @param string $metaKey
     * @return AbstractMeta|null
     */
    public function getMeta(string $metaKey): ?AbstractMeta
    {
        return $this->metas()
            ->firstWhere(AbstractMeta::META_KEY, $metaKey);
    }

    /**
     * @param string $metaKey
     * @return mixed|null
     */
    public function getMetaValue(string $metaKey)
    {
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
            ->where(AbstractMeta::META_KEY, $metaKey)
            ->exists();
    }

    /**
     * @param string $metaKey
     * @param $value
     * @return AbstractMeta|null
     */
    public function setMeta(string $metaKey, $value): ?AbstractMeta
    {
        $instance = $this->metas()
            ->firstOrNew([
                AbstractMeta::META_KEY => $metaKey
            ]);

        $instance->fill([
            AbstractMeta::META_VALUE => $value
        ])->save();

        return $instance;
    }

    /**
     * @param string $metaKey
     * @return bool
     */
    public function deleteMeta(string $metaKey): bool
    {
        return $this->metas()
            ->where(AbstractMeta::META_KEY, $metaKey)
            ->forceDelete();
    }

    /**
     * @return string
     */
    abstract protected function _getMetaClass(): string;

    /**
     * @return string
     */
    abstract protected function _getMetaFk(): string;
}
