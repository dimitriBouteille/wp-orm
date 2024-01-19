<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Api;

/**
 * @since 3.0.0
 */
interface CommentInterface
{
    public const COMMENT_ID = 'comment_ID';
    public const POST_ID = 'comment_post_ID';
    public const AUTHOR = 'comment_author';
    public const AUTHOR_EMAIL = 'comment_author_email';
    public const AUTHOR_URL = 'comment_author_url';
    public const AUTHOR_IP = 'comment_author_IP';
    public const DATE = 'comment_date';
    public const DATE_GMT = 'comment_date_gmt';
    public const CONTENT = 'comment_content';
    public const KARMA = 'comment_karma';
    public const APPROVED = 'comment_approved';
    public const AGENT = 'comment_agent';
    public const TYPE = 'comment_type';
    public const PARENT = 'comment_parent';
    public const USER_ID = 'user_id';
}
