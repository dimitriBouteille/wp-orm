<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Carbon\Carbon;
use Dbout\WpOrm\Builders\CommentBuilder;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
 * @method static CommentBuilder query()
 *
 * @property-read User|null $user
 * @property-read Post|null $post
 * @property-read Comment|null $parent
 */
class Comment extends AbstractModel
{
    final public const CREATED_AT = self::DATE;
    final public const UPDATED_AT =  null;
    final public const COMMENT_ID = 'comment_ID';
    final public const POST_ID = 'comment_post_ID';
    final public const AUTHOR = 'comment_author';
    final public const AUTHOR_EMAIL = 'comment_author_email';
    final public const AUTHOR_URL = 'comment_author_url';
    final public const AUTHOR_IP = 'comment_author_IP';
    final public const DATE = 'comment_date';
    final public const DATE_GMT = 'comment_date_gmt';
    final public const CONTENT = 'comment_content';
    final public const KARMA = 'comment_karma';
    final public const APPROVED = 'comment_approved';
    final public const AGENT = 'comment_agent';
    final public const TYPE = 'comment_type';
    final public const PARENT = 'comment_parent';
    final public const USER_ID = 'user_id';

    /**
     * @inheritDoc
     */
    protected $primaryKey = self::COMMENT_ID;

    /**
     * @inheritDoc
     */
    protected $table = 'comments';

    /**
     * @inheritDoc
     */
    protected $casts = [
        self::KARMA => 'integer',
        self::DATE_GMT => 'datetime',
        self::DATE => 'datetime',
    ];

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, User::USER_ID, self::USER_ID);
    }

    /**
     * @return HasOne
     */
    public function post(): HasOne
    {
        return $this->hasOne(Post::class, Post::POST_ID, self::POST_ID);
    }

    /**
     * @return HasOne
     */
    public function parent(): HasOne
    {
        return $this->hasOne(Comment::class, Comment::COMMENT_ID, self::PARENT);
    }

    /**
     * @inheritDoc
     */
    public function newEloquentBuilder($query): CommentBuilder
    {
        return new CommentBuilder($query);
    }

    /**
     * @param string $author
     * @return self
     * @deprecated Remove in next version
     * @see setCommentAuthor()
     */
    public function setAuthor(string $author): self
    {
        return $this->setCommentAuthor($author);
    }

    /**
     * @return string|null
     * @deprecated Remove in next version
     * @see getCommentAuthor()
     */
    public function getAuthor(): ?string
    {
        return $this->getCommentAuthor();
    }

    /**
     * @param string|null $email
     * @return self
     * @deprecated Remove in next version
     * @see setCommentAuthorEmail()
     */
    public function setAuthorEmail(?string $email): self
    {
        return $this->setCommentAuthorEmail($email);
    }

    /**
     * @return string|null
     * @deprecated Remove in next version
     * @see getCommentAuthorEmail()
     */
    public function getAuthorEmail(): ?string
    {
        return $this->getCommentAuthorEmail();
    }

    /**
     * @param string|null $url
     * @return self
     * @deprecated Remove in next version
     * @see setCommentAuthorUrl()
     */
    public function setAuthorUrl(?string $url): self
    {
        return $this->setCommentAuthorUrl($url);
    }

    /**
     * @return string|null
     * @deprecated Remove in next version
     * @see getCommentAuthorUrl()
     */
    public function getAuthorUrl(): ?string
    {
        return $this->getCommentAuthorUrl();
    }

    /**
     * @param string|null $ip
     * @return self
     * @deprecated Remove in next version
     * @see setCommentAuthorIP()
     */
    public function setAuthorIp(?string $ip): self
    {
        return $this->setCommentAuthorIP($ip);
    }

    /**
     * @return string|null
     * @deprecated Remove in next version
     * @see getCommentAuthorIP()
     */
    public function getAuthorIp(): ?string
    {
        return $this->getCommentAuthorIP();
    }

    /**
     * @param string|null $content
     * @return self
     * @deprecated Remove in next version
     * @see setCommentContent()
     */
    public function setContent(?string $content): self
    {
        return $this->setCommentContent($content);
    }

    /**
     * @return string|null
     * @deprecated Remove in next version
     * @see gsetCommentContent()
     */
    public function getContent(): ?string
    {
        return $this->getCommentContent();
    }

    /**
     * @param int|null $karma
     * @return self
     * @deprecated Remove in next version
     * @see setCommentKarma()
     */
    public function setKarma(?int $karma): self
    {
        return $this->setCommentKarma($karma);
    }

    /**
     * @inheritDoc
     * @deprecated Remove in next version
     * @see getCommentKarma()
     */
    public function getKarma(): ?int
    {
        return $this->getCommentKarma();
    }

    /**
     * @param string|null $agent
     * @return self
     * @deprecated Remove in next version
     * @see setCommentAgent()
     */
    public function setAgent(?string $agent): self
    {
        return $this->setCommentAgent($agent);
    }

    /**
     * @return string|null
     * @deprecated Remove in next version
     * @see getCommentAgent()
     */
    public function getAgent(): ?string
    {
        return $this->getCommentAgent();
    }

    /**
     * @param string|null $type
     * @return self
     * @deprecated Remove in next version
     * @see setCommentType()
     */
    public function setType(?string $type): self
    {
        return $this->setCommentType($type);
    }

    /**
     * @return string|null
     * @deprecated Remove in next version
     * @see getCommentType()
     */
    public function getType(): ?string
    {
        return $this->getCommentType();
    }

    /**
     * @param string|null $approved
     * @return self
     * @deprecated Remove in next version
     * @see setCommentApproved()
     */
    public function setApproved(?string $approved): self
    {
        return $this->setCommentApproved($approved);
    }

    /**
     * @return string|null
     * @deprecated Remove in next version
     * @see getCommentApproved()
     */
    public function getApproved(): ?string
    {
        return $this->getCommentApproved();
    }

    /**
     * @param mixed $date
     * @return self
     * @deprecated Remove in next version
     * @see setCommentDate()
     */
    public function setDate(mixed $date): self
    {
        return $this->setCommentDate($date);
    }

    /**
     * @return Carbon|null
     * @deprecated Remove in next version
     * @see getCommentDate()
     */
    public function getDate(): ?Carbon
    {
        return $this->getCommentDate();
    }

    /**
     * @param mixed $date
     * @return self
     * @deprecated Remove in next version
     * @see setCommentDateGmt()
     */
    public function setDateGMT(mixed $date): self
    {
        return $this->setCommentDateGmt($date);
    }

    /**
     * @return Carbon|null
     * @deprecated Remove in next version
     * @see getCommentDateGmt()
     */
    public function getDateGMT(): ?Carbon
    {
        return $this->getCommentDateGmt();
    }
}
