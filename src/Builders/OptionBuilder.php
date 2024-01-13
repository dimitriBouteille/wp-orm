<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Builders;

use Dbout\WpOrm\Api\OptionInterface;
use Dbout\WpOrm\Models\Option;

class OptionBuilder extends AbstractBuilder
{
    /**
     * @param string $optionName
     * @return Option|null
     */
    public function findOneByName(string $optionName): ?Option
    {
        /** @var Option|null $model */
        $model = $this->firstWhere(OptionInterface::NAME, $optionName);
        return $model;
    }

    /**
     * @param string $optionName
     * @return $this
     */
    public function whereName(string $optionName): self
    {
        return $this->where(OptionInterface::NAME, $optionName);
    }
}
