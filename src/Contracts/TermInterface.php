<?php

namespace Dbout\WpOrm\Contracts;

/**
 * Interface TermInterface
 * @package Dbout\WpOrm\Contracts
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
interface TermInterface
{

    const TERM_ID = 'term_id';
    const NAME = 'name';
    const SLUG = 'slug';
    const TERM_GROUP = 'term_group';

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     * @return TermInterface
     */
    public function setName(string $name): TermInterface;

    /**
     * @return string
     */
    public function getSlug(): string;

    /**
     * @param string $slug
     * @return TermInterface
     */
    public function setSlug(string $slug): TermInterface;

    /**
     * @return int|null
     */
    public function getTermGroup(): ?int;

    /**
     * @param int|null $termGroup
     * @return TermInterface
     */
    public function setTermGroup(?int $termGroup): TermInterface;
}
