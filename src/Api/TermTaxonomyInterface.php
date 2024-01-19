<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Api;

/**
 * @since 3.0.0
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
