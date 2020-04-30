<?php

namespace Dbout\WpOrm\Contracts;

/**
 * Interface PostMetaInterface
 * @package Dbout\WpOrm\Contracts
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
interface PostMetaInterface extends MetaInterface
{

    const META_ID = 'meta_id';
    const POST_ID = 'post_id';

    /**
     * @param $post
     * @return PostMetaInterface
     */
    public function setPost($post): PostMetaInterface;

    /**
     * @return mixed
     */
    public function getPost();

}