<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Api;

use Dbout\WpOrm\Models\TermRelationship;

/**
 * @method int|null getTermOrder()
 * @method TermRelationship setTermOrder(?int $order)
 * @method int|null getTermTaxonomyId()
 * @method TermRelationship setTermTaxonomyId(?int $id)
 * @method int|null getObjectId()
 * @method TermRelationship setObjectId(?int $id)
 */
interface TermRelationshipInterface
{
    public const OBJECT_ID = 'object_id';
    public const TERM_TAXONOMY_ID = 'term_taxonomy_id';
    public const TERM_ORDER = 'term_order';
}
