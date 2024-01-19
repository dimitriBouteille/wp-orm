<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Api\TermRelationshipInterface;
use Dbout\WpOrm\Orm\AbstractModel;

/**
 * @method int|null getTermOrder()
 * @method TermRelationship setTermOrder(?int $order)
 * @method int|null getTermTaxonomyId()
 * @method TermRelationship setTermTaxonomyId(?int $id)
 * @method int|null getObjectId()
 * @method TermRelationship setObjectId(?int $id)
 */
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
