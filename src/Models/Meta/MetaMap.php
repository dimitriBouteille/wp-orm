<?php

namespace Dbout\WpOrm\Models\Meta;

/**
 * Class MetaMap
 * @package Dbout\WpOrm\Models\Meta
 */
class MetaMap
{

    /**
     * @var string
     */
    protected string $class;

    /**
     * @var string
     */
    protected string $fk;

    /**
     * MetaMap constructor.
     * @param string $class
     * @param string $fk
     */
    public function __construct(string $class, string $fk)
    {
        $this->class = $class;
        $this->fk = $fk;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     * @return MetaMap
     */
    public function setClass(string $class): MetaMap
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @return string
     */
    public function getFk(): string
    {
        return $this->fk;
    }

    /**
     * @param string $fk
     * @return MetaMap
     */
    public function setFk(string $fk): MetaMap
    {
        $this->fk = $fk;
        return $this;
    }
}