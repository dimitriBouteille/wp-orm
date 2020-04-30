<?php

namespace Dbout\WpOrm\Contracts;

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
    const POST_DATE =' post_date';
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
     * Get post title
     *
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * Set post title
     *
     * @param string|null $title
     * @return PostInterface
     */
    public function setTitle(?string $title): PostInterface;

    /**
     * Get post content
     *
     * @return string|null
     */
    public function getContent(): ?string;

    /**
     * Set post content
     *
     * @param string|null $content
     * @return PostInterface
     */
    public function setContent(?string $content): PostInterface;

    /**
     * Get post status
     * ie : draft, publish, ...
     *
     * @return string|null
     */
    public function getStatus(): ?string;

    /**
     * Set post status
     *
     * @param string|null $status
     * @return PostInterface
     */
    public function setStatus(?string $status): PostInterface;

    /**
     * Get post excerpt
     *
     * @return string|null
     */
    public function getExcerpt(): ?string;

    /**
     * Set post excerpt
     *
     * @param string|null $excerpt
     * @return PostInterface
     */
    public function setExcerpt(?string $excerpt): PostInterface;

    /**
     * Get post author id
     *
     * @return int|null
     */
    public function getAuthor(): ?int;

    /**
     * Set post author id
     *
     * @param int|null $author
     * @return PostInterface
     */
    public function setAuthor(?int $author): PostInterface;

    /**
     * Get post type
     *
     * @return string|null
     */
    public function getPostType(): ?string;

    /**
     * Set post type
     *
     * @param string $postType
     * @return PostInterface
     */
    public function setPostType(string $postType): PostInterface;

    /**
     * Get created at
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * Get updated at
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

    /**
     * Get metas
     *
     * @return mixed
     */
    public function getMetas();

}