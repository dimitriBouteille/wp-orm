<?php

namespace Dbout\WpOrm\Contracts;

/**
 * Interface MetaInterface
 * @package Dbout\WpOrm\Contracts
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
interface MetaInterface
{

    const META_KEY = 'meta_key';
    const META_VALUE = 'meta_value';

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return string
     */
    public function getKey(): string;

    /**
     * @param string $key
     * @return UserMetaInterface
     */
    public function setKey(string $key): MetaInterface;

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @param string $value
     * @return MetaInterface
     */
    public function setValue(string $value): MetaInterface;

}