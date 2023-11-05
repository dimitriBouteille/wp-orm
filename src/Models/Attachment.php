<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Builders\PostBuilder;

/**
 * @method static|PostBuilder mimeType(string|array $type)
 */
class Attachment extends CustomPost
{
    /**
     * @inheritDoc
     */
    protected string $_type = 'attachment';

    /**
     * @param PostBuilder $builder
     * @param ...$types
     * @return void
     */
    protected function scopeMimeType(PostBuilder $builder, ...$types): void
    {
        $firstValue = reset($types);
        if (is_array($firstValue)) {
            $builder->whereIn(self::MIME_TYPE, $firstValue);
        } else {
            $builder->where(self::MIME_TYPE, $firstValue);
        }
    }
}
