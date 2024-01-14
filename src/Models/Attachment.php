<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Builders\PostBuilder;

/**
 * @method static|PostBuilder mimeType(string $type)
 */
class Attachment extends CustomPost
{
    /**
     * @inheritDoc
     */
    protected string $_type = 'attachment';

    /**
     * @param PostBuilder $builder
     * @param string $type
     * @return void
     */
    protected function scopeMimeType(PostBuilder $builder, string $type): void
    {
        $builder->where(self::MIME_TYPE, $type);
    }
}
