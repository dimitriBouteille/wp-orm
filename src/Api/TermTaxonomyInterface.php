<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Api;

use Dbout\WpOrm\Models\TermTaxonomy;

/**
 * @method int|null getTermId()
 * @method TermTaxonomy setTermId(int $id)
 * @method string getTaxonomy()
 * @method TermTaxonomy setTaxonomy(string $taxonomy)
 * @method string|null getDescription()
 * @method TermTaxonomy setDescription(?string $description)
 * @method int|null getParent()
 * @method TermTaxonomy setParent($parent)
 * @method int|null getCount()
 * @method TermTaxonomy setCount(int $count)
 */
interface TermTaxonomyInterface
{
    public const TERM_TAXONOMY_ID = 'term_taxonomy_id';
    public const TERM_ID = 'term_id';
    public const TAXONOMY = 'taxonomy';
    public const DESCRIPTION = 'description';
    public const PARENT = 'parent';
    public const COUNT = 'count';
}
