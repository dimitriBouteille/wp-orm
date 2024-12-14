<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Builders\TermBuilder;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @method string|null getName()
 * @method Term setName(?string $name);
 * @method string|null getSlug()
 * @method Term setSlug(?string $slug)
 * @method int|null getTermGroup()
 * @method Term setTermGroup(?int $group)
 * @method static TermBuilder query()
 *
 * @property-read TermTaxonomy|null $termTaxonomy
 */
class Term extends AbstractModel
{
    final public const TERM_ID = 'term_id';
    final public const NAME = 'name';
    final public const SLUG = 'slug';
    final public const TERM_GROUP = 'term_group';

    /**
     * @inheritDoc
     */
    protected $table = 'terms';

    /**
     * @inheritDoc
     */
    protected $primaryKey = self::TERM_ID;

    /**
     * @inheritDoc
     */
    public $timestamps = false;

    /**
     * @inheritDoc
     */
    protected $casts = [
        self::TERM_GROUP => 'integer',
    ];

    /**
     * @return HasOne
     */
    public function termTaxonomy(): HasOne
    {
        return $this->hasOne(
            TermTaxonomy::class,
            TermTaxonomy::TERM_ID,
            self::TERM_ID
        );
    }

    /**
     * @inheritDoc
     */
    public function newEloquentBuilder($query): TermBuilder
    {
        return new TermBuilder($query);
    }
}
