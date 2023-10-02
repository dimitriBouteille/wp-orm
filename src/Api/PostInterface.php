<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Api;

use Carbon\Carbon;
use Dbout\WpOrm\Models\Post;

/**
 * @method Post setPostDate($date)
 * @method Carbon|null getPostDate()
 * @method Post setPostDateGMT($date)
 * @method Carbon|null getPostDateGMT()
 * @method Post setPostContent(?string $content)
 * @method string|null getPostContent()
 * @method Post setPostType(string $type)
 * @method string|null getPostType()
 * @method Post setGuid(?string $guid)
 * @method string|null getGuid()
 * @method Post setPostTitle(?string $title)
 * @method string|null getPostTitle()
 * @method Post setPostExcerpt(?string $excerpt)
 * @method string|null getPostExcerpt()
 * @method Post setPostStatus(?string $status)
 * @method string|null getPostStatus()
 * @method Post setCommentStatus(string $status)
 * @method string|null getCommentStatus()
 * @method Post setPingStatus(string $status)
 * @method string|null getPingStatus()
 * @method Post setPostPassword(?string $password)
 * @method string|null getPostPassword()
 * @method Post setPostName(?string $name)
 * @method string|null getPostName()
 * @method Post setToPing(?string $toPing)
 * @method string|null getToPing()
 * @method Post setPinged(?string $pinged)
 * @method string|null getPinged()
 * @method Post setPostModified($modified)
 * @method Carbon|null getPostModified()
 * @method Post setPostModifiedGMT($modified)
 * @method Carbon|null getPostModifiedGMT()
 * @method setPostMimeType(?string $mimeType)
 * @method string|null getPostMimeType()
 * @method Post setMenuOrder(?int $order)
 * @method int|null getMenuOrder()
 * @method Post setPostContentFiltered($content)
 * @method string|null getPostContentFiltered()
 */
interface PostInterface
{
    public const POST_ID = 'ID';
    public const AUTHOR = 'post_author';
    public const DATE = 'post_date';
    public const DATE_GMT = 'post_date_gmt';
    public const CONTENT = 'post_content';
    public const TITLE = 'post_title';
    public const EXCERPT = 'post_excerpt';
    public const COMMENT_STATUS = 'comment_status';
    public const STATUS = 'post_status';
    public const PING_STATUS = 'ping_status';
    public const PASSWORD = 'post_password';
    public const POST_NAME = 'post_name';
    public const TO_PING = 'to_ping';
    public const PINGED = 'pinged';
    public const MODIFIED = 'post_modified';
    public const MODIFIED_GMT = 'post_modified_gmt';
    public const CONTENT_FILTERED = 'post_content_filtered';
    public const PARENT = 'post_parent';
    public const GUID = 'guid';
    public const MENU_ORDER = 'menu_order';
    public const TYPE = 'post_type';
    public const MIME_TYPE = 'post_mime_type';
    public const COMMENT_COUNT = 'comment_count';
}
