<?php


namespace Dbout\WpOrm\Contracts;

use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Interface TermTaxonomyInterface
 * @package Dbout\WpOrm\Contracts
 *
 * @property TermInterface|null $term
 * @property TermInterface|null $parentTerm
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
interface TermTaxonomyInterface
{

    const TERM_TAXONOMY_ID = 'term_taxonomy_id';
    const TERM_ID = 'term_id';
    const TAXONOMY = 'taxonomy';
    const DESCRIPTION = 'description';
    const PARENT = 'parent';
    const COUNT = 'count';

    /**
     * @return string|null
     */
    public function getTaxonomy(): ?string;

    /**
     * @param string|null $taxonomy
     * @return $this
     */
    public function setTaxonomy(?string $taxonomy): self;

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @param string|null $description
     * @return $this
     */
    public function setDescription(?string $description): self;

    /**
     * @return int
     */
    public function getCount(): int;

    /**
     * @param int $count
     * @return $this
     */
    public function setCount(int $count): self;

    /**
     * @return HasOne
     */
    public function term(): HasOne;

    /**
     * @return HasOne
     */
    public function parentTerm(): HasOne;
}