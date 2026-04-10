<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Models;

class Attachment extends CustomPost
{
    /**
     * @inheritDoc
     */
    protected string $_type = 'attachment';
}
