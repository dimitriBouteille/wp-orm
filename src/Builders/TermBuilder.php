<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Builders;

use Dbout\WpOrm\Models\TermTaxonomy;
use Illuminate\Database\Eloquent\Collection;

class TermBuilder extends AbstractBuilder
{
    /**
     * @param string $taxonomy
     * @return Collection
     */
    public function findAllByTaxonomy(string $taxonomy): Collection
    {
        return $this->whereHas('termTaxonomy', function ($query) use ($taxonomy) {
            return $query->where(TermTaxonomy::TAXONOMY, $taxonomy);
        })->get();
    }
}
