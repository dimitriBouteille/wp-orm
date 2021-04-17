<?php

namespace Dbout\WpOrm\Models\Meta;

/**
 * Interface MetaInterface
 * @package Dbout\WpOrm\Models\Meta
 */
interface MetaInterface
{

    /**
     * @return string
     */
    public function getFkColumn(): string;


    /**
     * @return string
     */
    public function getKeyColumn(): string;

    /**
     * @return string
     */
    public function getValueColumn(): string;
}