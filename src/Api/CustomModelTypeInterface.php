<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Dbout\WpOrm\Api;

interface CustomModelTypeInterface
{
    /**
     * @return string
     */
    public function getCustomTypeCode(): string;

    /**
     * @return string
     */
    public function getCustomTypeColumn(): string;
}
