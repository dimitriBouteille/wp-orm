<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Api\TermInterface;
use Dbout\WpOrm\Api\TermTaxonomyInterface;
use Dbout\WpOrm\Builders\TermBuilder;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @method static Term|null find(int $termId)
 * @method static TermBuilder query()
 * @property TermTaxonomy|null $termTaxonomy
 */
class Term extends AbstractModel implements TermInterface
{
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
     * @inheritDoc
     */
    protected $fillable = [
        self::SLUG,
        self::TERM_ID,
        self::TERM_GROUP,
        self::NAME,
    ];

    /**
     * @return HasOne
     */
    public function termTaxonomy(): HasOne
    {
        return $this->hasOne(
            TermTaxonomy::class,
            TermTaxonomyInterface::TERM_ID,
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
