<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Orm\AbstractModel;

/**
 * Class TermRelationship
 * @package Dbout\WpOrm\Models
 *
 * @method int|null getTermOrder()
 * @method self setTermOrder(?int $order)
 * @method int|null getTermTaxonomyId()
 * @method self setTermTaxonomyId(?int $id)
 * @method int|null getObjectId()
 * @method self setObjectId(?int $id)
 */
class TermRelationship extends AbstractModel
{

    const OBJECT_ID = 'object_id';
    const TERM_TAXONOMY_ID = 'term_taxonomy_id';
    const TERM_ORDER = 'term_order';

    /**
     * @var string
     */
    protected $primaryKey = self::OBJECT_ID;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $casts = [
        self::TERM_ORDER => 'integer',
        self::TERM_TAXONOMY_ID => 'integer',
    ];
}