<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Dbout\WpOrm\Enums;

enum PingStatus: string
{
    case Closed = 'closed';
    case Open = 'open';
}
