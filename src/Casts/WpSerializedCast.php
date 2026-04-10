<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Cast that handles WordPress serialized values.
 *
 * WordPress uses PHP serialization to store arrays and objects in the database
 * (e.g., in the options table). This cast automatically unserializes values when
 * reading and serializes them when writing, matching WordPress core behavior.
 *
 * @implements CastsAttributes<mixed, mixed>
 */
class WpSerializedCast implements CastsAttributes
{
    /**
     * @inheritDoc
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        return self::maybeUnserialize($value);
    }

    /**
     * @inheritDoc
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_array($value) || is_object($value)) {
            return serialize($value);
        }

        return $value;
    }

    /**
     * Unserialize value only if it was serialized.
     * This is a pure PHP implementation of WordPress maybe_unserialize().
     *
     * @param string $value
     * @return mixed
     */
    public static function maybeUnserialize(string $value): mixed
    {
        if (!self::isSerialized($value)) {
            return $value;
        }

        return @unserialize($value, ['allowed_classes' => false]);
    }

    /**
     * Check if a value is serialized.
     * This is a pure PHP implementation of WordPress is_serialized().
     *
     * @see https://developer.wordpress.org/reference/functions/is_serialized/
     * @param string $data
     * @return bool
     */
    public static function isSerialized(string $data): bool
    {
        if ($data === 'b:0;' || $data === 'N;') {
            return true;
        }

        if (strlen($data) < 4) {
            return false;
        }

        if ($data[1] !== ':') {
            return false;
        }

        $lastChar = $data[-1];

        return match ($data[0]) {
            's' => $lastChar === '"' || str_ends_with($data, '";'),
            'a', 'O', 'E' => $lastChar === '}',
            'b', 'i', 'd' => $lastChar === ';',
            default => false,
        };
    }
}
