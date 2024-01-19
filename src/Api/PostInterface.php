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
