<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Api\TermTaxonomyInterface;
use Dbout\WpOrm\Orm\AbstractModel;

/**
 * @method static TermTaxonomy|null find(int $id)
 */
class TermTaxonomy extends AbstractModel implements TermTaxonomyInterface
{
    /**
     * @inheritDoc
     */
    public $timestamps = false;

    /**
     * @inheritDoc
     */
    protected $table = 'term_taxonomy';

    /**
     * @inheritDoc
     */
    protected $primaryKey = self::TERM_TAXONOMY_ID;

    /**
     * @inheritDoc
     */
    protected $casts = [
        self::COUNT => 'integer',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        self::TERM_TAXONOMY_ID, self::TERM_ID, self::TAXONOMY, self::DESCRIPTION, self::PARENT, self::COUNT,
    ];
}
