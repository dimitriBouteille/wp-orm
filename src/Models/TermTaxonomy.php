<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Contracts\TermTaxonomyInterface;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class TermTaxonomy
 * @package Dbout\WpOrm\Models
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
class TermTaxonomy extends AbstractModel implements TermTaxonomyInterface
{

    /**
     * @var string
     */
    protected $table = 'term_taxonomy';

    /**
     * @var string
     */
    protected $primaryKey = self::TERM_TAXONOMY_ID;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return string|null
     */
    public function getTaxonomy(): ?string
    {
        return $this->getAttribute(self::TAXONOMY);
    }

    /**
     * @param string|null $taxonomy
     * @return TermTaxonomyInterface
     */
    public function setTaxonomy(?string $taxonomy): TermTaxonomyInterface
    {
        $this->setAttribute(self::TAXONOMY, $taxonomy);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->getAttribute(self::DESCRIPTION);
    }

    /**
     * @param string|null $description
     * @return TermTaxonomyInterface
     */
    public function setDescription(?string $description): TermTaxonomyInterface
    {
        $this->setAttribute(self::DESCRIPTION);
        return $this;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
       return (int)$this->getAttribute(self::COUNT);
    }

    /**
     * @param int $count
     * @return TermTaxonomyInterface
     */
    public function setCount(int $count): TermTaxonomyInterface
    {
        $this->setAttribute(self::COUNT, $count);
        return $this;
    }

    /**
     * @return HasOne
     */
    public function term(): HasOne
    {
        return $this->hasOne(Term::class, Term::TERM_ID, self::TERM_ID);
    }

    /**
     * @return HasOne
     */
    public function parentTerm(): HasOne
    {
        return $this->hasOne(Term::class, Term::TERM_ID, self::PARENT);
    }
}
