<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Dbout\WpOrm\Models;

class Page extends CustomPost
{
    /**
     * @inheritDoc
     */
    protected string $_type = 'page';
}
