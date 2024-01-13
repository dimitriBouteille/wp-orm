<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models\Traits;

use Dbout\WpOrm\Exceptions\NotAllowedException;

trait HasCustomType
{
    /**
     * @var string
     */
    protected string $_type;

    /**
     * @return string|null
     */
    public function getPostType(): ?string
    {
        return $this->_type;
    }

    /**
     * @param string $postType
     * @throws NotAllowedException
     */
    final public function setPostType(string $postType): never
    {
        throw new NotAllowedException(sprintf(
            'You cannot set a type for this object. Current type [%s]',
            $this->_type
        ));
    }

    /**
     * @return string|null
     */
    public static function type(): ?string
    {
        return (new static())->getPostType();
    }
}
