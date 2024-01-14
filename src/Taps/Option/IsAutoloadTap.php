<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Taps\Option;

use Dbout\WpOrm\Api\OptionInterface;
use Dbout\WpOrm\Builders\OptionBuilder;
use Dbout\WpOrm\Enums\YesNo;

/**
 * @since 3.0.0
 */
class IsAutoloadTap
{
    public function __construct(
        protected readonly bool|YesNo $autoload = YesNo::Yes
    ) {
    }

    /**
     * @param OptionBuilder $builder
     * @return void
     */
    public function __invoke(OptionBuilder $builder): void
    {
        $autoload = $this->autoload;
        if (is_bool($autoload)) {
            $autoload = $autoload ? YesNo::Yes : YesNo::No;
        }

        $builder->where(OptionInterface::AUTOLOAD, $autoload->value);
    }
}
