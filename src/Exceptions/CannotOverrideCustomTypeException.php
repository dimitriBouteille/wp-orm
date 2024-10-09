<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Exceptions;

class CannotOverrideCustomTypeException extends NotAllowedException
{
    /**
     * @param string $type
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $type, int $code = 0, ?\Throwable $previous = null)
    {
        $message = sprintf(
            'You cannot override type for this object. Current type [%s]',
            $type
        );

        parent::__construct($message, $code, $previous);
    }
}
