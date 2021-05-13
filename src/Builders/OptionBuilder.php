<?php

namespace Dbout\WpOrm\Builders;

use Dbout\WpOrm\Models\Option;

/**
 * Class OptionBuilder
 * @package Dbout\WpOrm\Builders
 */
class OptionBuilder extends AbstractBuilder
{

    /**
     * @param string $optionName
     * @return Option|null
     */
    public function findOneByName(string $optionName): ?Option
    {
        return $this->firstWhere(Option::NAME, $optionName);
    }

    /**
     * @param string $optionName
     * @return $this
     */
    public function whereName(string $optionName): self
    {
        return $this->where(Option::NAME, $optionName);
    }
}
