<?php

namespace Dbout\WpOrm\Builders;

use Dbout\WpOrm\Contracts\TermInterface;
use Dbout\WpOrm\Models\Term;
use Dbout\WpOrm\Models\TermTaxonomy;
use Dbout\WpOrm\Models\TermType;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TermTypeBuilder
 * @package Dbout\WpOrm\Builders
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
class TermTypeBuilder extends Builder
{

    /**
     * @var TermType
     */
    protected $model;

    /**
     * @param array $columns
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function get($columns = ['*'])
    {
        $taxonomy = $this->model->getTaxonomy();

        $t1 = Term::table();
        $t2 = TermTaxonomy::table();

        $this->query->join($t2, $t1.'.'.Term::TERM_ID, $t2.'.'. TermTaxonomy::TERM_ID)
            ->where($t2.'.'. TermTaxonomy::TAXONOMY, $taxonomy);

        return parent::get($columns);
    }
}
