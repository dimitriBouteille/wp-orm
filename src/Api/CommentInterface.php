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
use Dbout\WpOrm\Models\Post;
use Dbout\WpOrm\Models\User;

/**
 * @method Comment setCommentAuthor(?string $author)
 * @method string|null getCommentAuthor()
 * @method Comment setCommentAuthorEmail(?string $email)
 * @method string|null getCommentAuthorEmail()
 * @method Comment setCommentAuthorUrl(?string $url)
 * @method string|null getCommentAuthorUrl()
 * @method Comment setCommentAuthorIP(?string $ip)
 * @method string|null getCommentAuthorIP()
 * @method Comment setCommentContent(?string $content)
 * @method string|null getCommentContent()
 * @method Comment setCommentKarma(?int $karma)
 * @method int|null getCommentKarma()
 * @method Comment setCommentApproved(string $approved)
 * @method string getCommentApproved()
 * @method Comment setCommentAgent(?string $agent)
 * @method string|null getCommentAgent()
 * @method Comment setCommentType(?string $type)
 * @method string|null getCommentType()
 * @method Comment setUserId(?int $userId)
 * @method int|null getUserId()
 * @method Comment setCommentDate(mixed $date)
 * @method Carbon|null getCommentDate()
 * @method Comment setCommentDateGmt(mixed $date)
 * @method Carbon|null getCommentDateGmt()
 *
 * @property User|null $user
 * @property Post|null $post
 * @property Comment|null $parent
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

    /**
     * @param string $author
     * @return self
     * @deprecated Remove in next version
     * @see setCommentAuthor()
     */
    public function setAuthor(string $author): self;

    /**
     * @return string|null
     * @deprecated Remove in next version
     * @see getCommentAuthor()
     */
    public function getAuthor(): ?string;

    /**
     * @param string|null $email
     * @return self
     * @deprecated Remove in next version
     * @see setCommentAuthorEmail()
     */
    public function setAuthorEmail(?string $email): self;

    /**
     * @return string|null
     * @deprecated Remove in next version
     * @see getCommentAuthorEmail()
     */
    public function getAuthorEmail(): ?string;

    /**
     * @param string|null $url
     * @return self
     * @deprecated Remove in next version
     * @see setCommentAuthorUrl()
     */
    public function setAuthorUrl(?string $url): self;

    /**
     * @return string|null
     * @deprecated Remove in next version
     * @see getCommentAuthorUrl()
     */
    public function getAuthorUrl(): ?string;

    /**
     * @param string|null $ip
     * @return self
     * @deprecated Remove in next version
     * @see setCommentAuthorIP()
     */
    public function setAuthorIp(?string $ip): self;

    /**
     * @return string|null
     * @deprecated Remove in next version
     * @see getCommentAuthorIP()
     */
    public function getAuthorIp(): ?string;

    /**
     * @param string|null $content
     * @return self
     * @deprecated Remove in next version
     * @see setCommentContent()
     */
    public function setContent(?string $content): self;

    /**
     * @return string|null
     * @deprecated Remove in next version
     * @see gsetCommentContent()
     */
    public function getContent(): ?string;

    /**
     * @param int|null $karma
     * @return self
     * @deprecated Remove in next version
     * @see setCommentKarma()
     */
    public function setKarma(?int $karma): self;

    /**
     * @return int|null
     * @deprecated Remove in next version
     * @see getCommentKarma()
     */
    public function getKarma(): ?int;

    /**
     * @param string|null $agent
     * @return self
     * @deprecated Remove in next version
     * @see setCommentAgent()
     */
    public function setAgent(?string $agent): self;

    /**
     * @return string|null
     * @deprecated Remove in next version
     * @see getCommentAgent()
     */
    public function getAgent(): ?string;

    /**
     * @param string|null $type
     * @return self
     * @deprecated Remove in next version
     * @see setCommentType()
     */
    public function setType(?string $type): self;

    /**
     * @return string|null
     * @deprecated Remove in next version
     * @see getCommentType()
     */
    public function getType(): ?string;

    /**
     * @param string|null $approved
     * @return self
     * @deprecated Remove in next version
     * @see setCommentApproved()
     */
    public function setApproved(?string $approved): self;

    /**
     * @return string|null
     * @deprecated Remove in next version
     * @see getCommentApproved()
     */
    public function getApproved(): ?string;

    /**
     * @param mixed $date
     * @return self
     * @deprecated Remove in next version
     * @see setCommentDate()
     */
    public function setDate(mixed $date): self;

    /**
     * @return Carbon|null
     * @deprecated Remove in next version
     * @see getCommentDate()
     */
    public function getDate(): ?Carbon;

    /**
     * @param mixed $date
     * @return self
     * @deprecated Remove in next version
     * @see setCommentDateGmt()
     */
    public function setDateGMT(mixed $date): self;

    /**
     * @return Carbon|null
     * @deprecated Remove in next version
     * @see getCommentDateGmt()
     */
    public function getDateGMT(): ?Carbon;
}
