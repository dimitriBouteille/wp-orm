<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Orm\AbstractModel;

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
class TermTaxonomy extends AbstractModel
{
    final public const TERM_TAXONOMY_ID = 'term_taxonomy_id';
    final public const TERM_ID = 'term_id';
    final public const TAXONOMY = 'taxonomy';
    final public const DESCRIPTION = 'description';
    final public const PARENT = 'parent';
    final public const COUNT = 'count';

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
     * @inheritDoc
     */
    protected $fillable = [
        self::TERM_TAXONOMY_ID,
        self::TERM_ID,
        self::TAXONOMY,
        self::DESCRIPTION,
        self::PARENT,
        self::COUNT,
    ];
}
