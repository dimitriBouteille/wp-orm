<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Dbout\WpOrm\Builders;

use Dbout\WpOrm\Models\Option;

class OptionBuilder extends AbstractBuilder
{
    /**
     * @param string $optionName
     * @return $this
     */
    public function whereName(string $optionName): self
    {
        return $this->where(Option::NAME, $optionName);
    }
}
