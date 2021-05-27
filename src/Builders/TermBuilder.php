<?php

namespace Dbout\WpOrm\Builders;

use Dbout\WpOrm\Models\TermTaxonomy;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class TermBuilder
 * @package Dbout\WpOrm\Builders
 */
class TermBuilder extends AbstractBuilder
{

    /**
     * @param string $taxonomy
     * @return Collection
     */
    public function findAllByTaxonomy(string $taxonomy): Collection
    {
        return $this->whereHas('termTaxonomy', function ($query) use($taxonomy) {
            return $query->where(TermTaxonomy::TAXONOMY, $taxonomy);
        })->get();
    }
}