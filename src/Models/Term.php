<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Builders\TermBuilder;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @method static self find(int $termId)
 * @method static TermBuilder query()
 *
 * @method string|null getName()
 * @method self setName(?string $name);
 * @method string|null getSlug()
 * @method self setSlug(?string $slug)
 * @method int|null getTermGroup()
 * @method self setTermGroup(?int $group)
 *
 * @property TermTaxonomy|null $termTaxonomy
 */
class Term extends AbstractModel
{
    public const TERM_ID = 'term_id';
    public const NAME = 'name';
    public const SLUG = 'slug';
    public const TERM_GROUP = 'term_group';

    /**
     * @var string
     */
    protected $table = 'terms';

    /**
     * @var string
     */
    protected $primaryKey = self::TERM_ID;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $casts = [
        self::TERM_GROUP => 'integer',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        self::SLUG, self::TERM_ID, self::TERM_GROUP, self::NAME,
    ];

    /**
     * @return HasOne
     */
    public function termTaxonomy(): HasOne
    {
        return $this->hasOne(TermTaxonomy::class, TermTaxonomy::TERM_ID, self::TERM_ID);
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return TermBuilder
     */
    public function newEloquentBuilder($query): TermBuilder
    {
        return new TermBuilder($query);
    }
}
