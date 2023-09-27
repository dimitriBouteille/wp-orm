<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Api;

/**
 * @method int|null getTermOrder()
 * @method self setTermOrder(?int $order)
 * @method int|null getTermTaxonomyId()
 * @method self setTermTaxonomyId(?int $id)
 * @method int|null getObjectId()
 * @method self setObjectId(?int $id)
 */
interface TermRelationshipInterface
{
    public const OBJECT_ID = 'object_id';
    public const TERM_TAXONOMY_ID = 'term_taxonomy_id';
    public const TERM_ORDER = 'term_order';
}
