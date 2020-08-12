<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Builders\TermTypeBuilder;
use Dbout\WpOrm\Orm\AbstractModel;

/**
 * Class TermType
 * @package Dbout\WpOrm\Models
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
abstract class TermType extends Term
{

    /**
     * @var string
     */
    protected $taxonomy;

    /**
     * @return string
     */
    public function getTaxonomy(): string
    {
        return $this->taxonomy;
    }

    /**
     * Returns term taxonomy slug
     *
     * @return string|null
     */
    public static function taxonomy(): ?string
    {
        return (new static())->getTaxonomy();
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return TermTypeBuilder|AbstractModel|\Illuminate\Database\Eloquent\Builder
     */
    public function newEloquentBuilder($query)
    {
        return new TermTypeBuilder($query);
    }
}