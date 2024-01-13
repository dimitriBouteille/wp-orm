<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Api;

use Dbout\WpOrm\Models\Term;

/**
 * @method string|null getName()
 * @method Term setName(?string $name);
 * @method string|null getSlug()
 * @method Term setSlug(?string $slug)
 * @method int|null getTermGroup()
 * @method Term setTermGroup(?int $group)
 */
interface TermInterface
{
    public const TERM_ID = 'term_id';
    public const NAME = 'name';
    public const SLUG = 'slug';
    public const TERM_GROUP = 'term_group';
}
