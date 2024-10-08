<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Enums;

/**
 * @since 3.0.0
 */
enum PingStatus: string
{
    case Closed = 'closed';
    case Open = 'open';
}
