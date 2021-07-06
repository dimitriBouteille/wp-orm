<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Orm\AbstractModel;

/**
 * Class TermTaxonomy
 * @package Dbout\WpOrm\Models
 *
 * @method static TermTaxonomy|null find(int $id)
 * @method int|null getTermId()
 * @method self setTermId(int $id)
 * @method string getTaxonomy()
 * @method self setTaxonomy(string $taxonomy)
 * @method string|null getDescription()
 * @method self setDescription(?string $description)
 * @method int|null getParent()
 * @method self setParent($parent)
 * @method int|null getCount()
 * @method self setCount(int $count)
 */
class TermTaxonomy extends AbstractModel
{

    const TERM_TAXONOMY_ID = 'term_taxonomy_id';
    const TERM_ID = 'term_id';
    const TAXONOMY = 'taxonomy';
    const DESCRIPTION = 'description';
    const PARENT = 'parent';
    const COUNT = 'count';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $table = 'term_taxonomy';

    /**
     * @var string
     */
    protected $primaryKey = self::TERM_TAXONOMY_ID;

    /**
     * @var string[]
     */
    protected $casts = [
        self::COUNT => 'integer',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        self::TERM_TAXONOMY_ID, self::TERM_ID, self::TAXONOMY, self::DESCRIPTION, self::PARENT, self::COUNT
    ];
}