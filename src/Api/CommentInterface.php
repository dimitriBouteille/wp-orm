<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Api;

use Carbon\Carbon;
use Dbout\WpOrm\Models\Comment;

/**
 * @method Comment setAuthor(string $author)
 * @method string|null getAuthor()
 * @method Comment setAuthorEmail(string $email)
 * @method string|null getAuthorEmail()
 * @method Comment setAuthorUrl(?string $url)
 * @method string|null getAuthorUrl()
 * @method Comment setAuthorIp(?string $ip)
 * @method string|null getAuthorIp()
 * @method Comment setDate($date)
 * @method Carbon|null getDate()
 * @method Comment setDateGMT($date)
 * @method Carbon|null getDateGMT()
 * @method Comment setContent(string $content)
 * @method string|null getContent()
 * @method Comment setKarma(int $karma)
 * @method int|null getKarma()
 * @method Comment setAgent(string $agent)
 * @method string|null getAgent()
 * @method Comment setType(?string $type)
 * @method string|null getType()
 * @method Comment setApproved(string $approved)
 * @method string|null getApproved()
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
