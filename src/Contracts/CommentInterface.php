<?php

namespace Dbout\WpOrm\Contracts;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @param string $author
     * @return CommentInterface
     */
    public function setAuthor(string $author): CommentInterface;

    /**
     * @return string
     */
    public function getAuthor(): string;

    /**
     * @param string $email
     * @return CommentInterface
     */
    public function setAuthorEmail(string $email): CommentInterface;

    /**
     * @return string
     */
    public function getAuthorEmail(): string;

    /**
     * @param string|null $url
     * @return CommentInterface
     */
    public function setAuthorUrl(?string $url): CommentInterface;

    /**
     * @return string|null
     */
    public function getAuthorUrl(): ?string;

    /**
     * @param string|null $ip
     * @return CommentInterface
     */
    public function setAuthorIp(?string $ip): CommentInterface;

    /**
     * @return string|null
     */
    public function getAuthorIp(): ?string;

    /**
     * @param $date
     * @return CommentInterface
     */
    public function setDate($date): CommentInterface;

    /**
     * @return mixed
     */
    public function getDate();

    /**
     * @param $date
     * @return CommentInterface
     */
    public function setDateGMT($date): CommentInterface;

    /**
     * @return mixed
     */
    public function getDateGMT();

    /**
     * @param string $content
     * @return CommentInterface
     */
    public function setContent(string $content): CommentInterface;

    /**
     * @return string|null
     */
    public function getContent(): ?string;

    /**
     * @param int $karma
     * @return CommentInterface
     */
    public function setKarma(int $karma): CommentInterface;

    /**
     * @return int
     */
    public function getKarma(): int;

    /**
     * @param string|null $approved
     * @return CommentInterface
     */
    public function setApproved(string $approved): CommentInterface;

    /**
     * @return mixed
     */
    public function getApproved();

    /**
     * @param string $agent
     * @return CommentInterface
     */
    public function setAgent(string $agent): CommentInterface;

    /**
     * @return string|null
     */
    public function getAgent(): ?string;

    /**
     * @param string|null $type
     * @return CommentInterface
     */
    public function setType(?string $type): CommentInterface;

    /**
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * @return HasOne
     */
    public function user(): HasOne;

    /**
     * @return HasOne
     */
    public function post(): HasOne;

    /**
     * @return HasMany
     */
    public function metas(): HasMany;

}