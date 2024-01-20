<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Builders;

use Dbout\WpOrm\Models\Option;

class OptionBuilder extends AbstractBuilder
{
    /**
     * @param string $optionName
     * @return Option|null
     * @deprecated Remove in next version
     * @see Option::findOneByName()
     */
    public function findOneByName(string $optionName): ?Option
    {
        /** @var Option|null $model */
        $model = $this->firstWhere(Option::NAME, $optionName);
        return $model;
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
