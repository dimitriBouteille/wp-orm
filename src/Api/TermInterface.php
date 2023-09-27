<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Api;

/**
 * @method string|null getName()
 * @method self setName(?string $name);
 * @method string|null getSlug()
 * @method self setSlug(?string $slug)
 * @method int|null getTermGroup()
 * @method self setTermGroup(?int $group)
 */
interface TermInterface
{
    public const TERM_ID = 'term_id';
    public const NAME = 'name';
    public const SLUG = 'slug';
    public const TERM_GROUP = 'term_group';
}
