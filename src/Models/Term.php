<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Builders\TermTypeBuilder;
use Dbout\WpOrm\Contracts\TermInterface;
use Dbout\WpOrm\Contracts\TermTaxonomyInterface;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Term
 * @package Dbout\WpOrm\Models
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
class Term extends AbstractModel implements TermInterface
{

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
     * @param string $slug
     * @return $this
     */
    public function setSlug(string $slug): TermInterface
    {
        $this->setAttribute(self::SLUG, $slug);
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->getAttribute(self::SLUG);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): TermInterface
    {
        $this->setAttribute(self::NAME);
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getAttribute(self::NAME);
    }

    /**
     * @return HasOne
     */
    public function termTaxonomy(): HasOne
    {
        return $this->hasOne(TermTaxonomy::class, TermTaxonomy::TERM_ID);
    }
}
