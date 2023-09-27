<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Api\TermRelationshipInterface;
use Dbout\WpOrm\Orm\AbstractModel;

class TermRelationship extends AbstractModel implements TermRelationshipInterface
{
    /**
     * @inheritDoc
     */
    protected $primaryKey = self::OBJECT_ID;

    /**
     * @inheritDoc
     */
    public $timestamps = false;

    /**
     * @inheritDoc
     */
    protected $casts = [
        self::TERM_ORDER => 'integer',
        self::TERM_TAXONOMY_ID => 'integer',
    ];
}
