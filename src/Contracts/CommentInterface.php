<?php

namespace Dbout\WpOrm\Contracts;

/**
 * Interface CommentInterface
 * @package Dbout\WpOrm\Contracts
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
interface CommentInterface
{

    const COMMENT_ID = 'comment_ID';
    const COMMENT_POST_ID = 'comment_post_ID';
    const COMMENT_AUTHOR = 'comment_author';
    const COMMENT_AUTHOR_EMAIL = 'comment_author_email';
    const COMMENT_AUTHOR_URL = 'comment_author_url';
    const COMMENT_AUTHOR_IP = 'comment_author_IP';
    const COMMENT_DATE = 'comment_date';
    const COMMENT_DATE_GMT = 'comment_date_gmt';
    const COMMENT_CONTENT = 'comment_content';
    const COMMENT_KARMA = 'comment_karma';
    const COMMENT_APPROVED = 'comment_approved';
    const COMMENT_AGENT = 'comment_agent';
    const COMMENT_TYPE = 'comment_type';
    const COMMENT_PARENT = 'comment_parent';
    const USER_ID = 'user_id';

}