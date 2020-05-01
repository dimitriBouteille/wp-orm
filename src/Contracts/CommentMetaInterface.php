<?php

namespace Dbout\WpOrm\Contracts;

use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Interface CommentMetaInterface
 * @package Dbout\WpOrm\Contracts
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
interface CommentMetaInterface extends MetaInterface
{

    const META_ID = 'meta_id';
    const COMMENT_ID = 'comment_id';

    /**
     * @return HasOne
     */
    public function comment(): HasOne;
}