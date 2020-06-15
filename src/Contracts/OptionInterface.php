<?php

namespace Dbout\WpOrm\Contracts;

/**
 * Interface OptionInterface
 * @package Dbout\WpOrm\Contracts
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
interface OptionInterface
{

    const OPTION_ID = 'option_id';
    const OPTION_NAME = 'option_name';
    const OPTION_VALUE = 'option_value';
    const AUTOLOAD = 'autoload';

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
     * @return OptionInterface
     */
    public function setName(string $name): OptionInterface;

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @param $value
     * @return OptionInterface
     */
    public function setValue($value): OptionInterface;

    /**
     * @return string|null
     */
    public function getAutoload(): ?string;

    /**
     * @param string $autoload
     * @return OptionInterface
     */
    public function setAutoload(string $autoload): OptionInterface;
}
