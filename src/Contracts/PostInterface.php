<?php

namespace Dbout\WpOrm\Contracts;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Interface PostInterface
 * @package Dbout\WpOrm\Contracts
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
interface PostInterface
{

    const POST_ID = 'ID';
    const POST_AUTHOR = 'post_author';
    const POST_DATE = 'post_date';
    const POST_DATE_GMT = 'post_date_gmt';
    const POST_CONTENT = 'post_content';
    const POST_TITLE = 'post_title';
    const POST_EXCERPT = 'post_excerpt';
    const COMMENT_STATUS = 'comment_status';
    const POST_STATUS = 'post_status';
    const PING_STATUS = 'ping_status';
    const POST_PASSWORD = 'post_password';
    const POST_NAME = 'post_name';
    const TO_PING = 'to_ping';
    const PINGED = 'pinged';
    const POST_MODIFIED = 'post_modified';
    const POST_MODIFIED_GMT = 'post_modified_gmt';
    const POST_CONTENT_FILTERED = 'post_content_filtered';
    const POST_PARENT = 'post_parent';
    const GUID = 'guid';
    const MENU_ORDER = 'menu_order';
    const POST_TYPE = 'post_type';
    const POST_MIME_TYPE = 'post_mime_type';
    const COMMENT_COUNT = 'comment_count';

    /**
     * Get post id
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return mixed
     */
    public function getDate();

    /**
     * @param $date
     * @return PostInterface
     */
    public function setDate($date): PostInterface;

    /**
     * @return mixed
     */
    public function getDateGMT();

    /**
     * @param $date
     * @return PostInterface
     */
    public function setDateGMT($date): PostInterface;

    /**
     * @return string|null
     */
    public function getContent(): ?string;

    /**
     * @param string|null $content
     * @return PostInterface
     */
    public function setContent(?string $content): PostInterface;

    /**
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * @param string|null $title
     * @return PostInterface
     */
    public function setTitle(?string $title): PostInterface;

    /**
     * @return string|null
     */
    public function getExcerpt(): ?string;

    /**
     * @param string|null $excerpt
     * @return PostInterface
     */
    public function setExcerpt(?string $excerpt): PostInterface;

    /**
     * @return string|null
     */
    public function getStatus(): ?string;

    /**
     * @param string|null $status
     * @return PostInterface
     */
    public function setStatus(?string $status): PostInterface;

    /**
     * @return string
     */
    public function getPingStatus(): string;

    /**
     * @param string $status
     * @return PostInterface
     */
    public function setPingStatus(string $status): PostInterface;

    /**
     * @return string|null
     */
    public function getPassword(): ?string;

    /**
     * @param string|null $password
     * @return PostInterface
     */
    public function setPassword(?string $password): PostInterface;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string|null $name
     * @return PostInterface
     */
    public function setName(?string $name): PostInterface;

    /**
     * @return string|null
     */
    public function getToPing(): ?string;

    /**
     * @param string|null $toPing
     * @return PostInterface
     */
    public function setToPing(?string $toPing): PostInterface;

    /**
     * @return string|null
     */
    public function getPinged(): ?string;

    /**
     * @param string|null $pinged
     * @return PostInterface
     */
    public function setPinged(?string $pinged): PostInterface;

    /**
     * @return mixed
     */
    public function getModified();

    /**
     * @param $modified
     * @return PostInterface
     */
    public function setModified($modified): PostInterface;

    /**
     * @return mixed
     */
    public function getPostModifiedGMT();

    /**
     * @param $modified
     * @return PostInterface
     */
    public function setPostModifiedGMT($modified): PostInterface;

    /**
     * @return string|null
     */
    public function getPostType(): ?string;

    /**
     * @param string $postType
     * @return PostInterface
     */
    public function setPostType(string $postType): PostInterface;

    /**
     * @return string|null
     */
    public function getGuid(): ?string;

    /**
     * @param string|null $guid
     * @return PostInterface
     */
    public function setGuid(?string $guid): PostInterface;

    /**
     * @return string|null
     */
    public function getMimeType(): ?string;

    /**
     * @param string|null $mimeType
     * @return PostInterface
     */
    public function setMimeType(?string $mimeType): PostInterface;

    /**
     * @return string
     */
    public function getCommentStatus(): string;

    /**
     * @param string $status
     * @return PostInterface
     */
    public function setCommentStatus(string $status): PostInterface;

    /**
     * @return int
     */
    public function getMenuOrder(): int;

    /**
     * @param int $order
     * @return PostInterface
     */
    public function setMenuOrder(int $order): PostInterface;

    /**
     * @return HasMany
     */
    public function metas(): HasMany;

    /**
     * @return HasOne
     */
    public function author(): HasOne;

    /**
     * @return HasMany
     */
    public function comments(): HasMany;

    /**
     * @return HasOne
     */
    public function parent(): HasOne;

}