<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
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
 * @method Comment setCommentPostID(int $postId)
 * @method int|null getCommentPostID()
 * @method Comment setCommentParent(?int $parentId)
 * @method int|null getCommentParent()
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
        self::USER_ID => 'integer',
        self::POST_ID => 'integer',
        self::PARENT => 'integer',
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
     * @return int|null
     * @see getCommentPostID()
     */
    public function getCommentPostIdAttribute(): ?int
    {
        return $this->getAttributes()[self::POST_ID] ?? null;
    }

    /**
     * @param int|null $postId
     * @return $this
     * @see setCommentPostID()
     */
    public function setCommentPostIdAttribute(?int $postId): self
    {
        $this->attributes[self::POST_ID] = $postId;
        return $this;
    }

    /**
     * @return string|null
     * @see getCommentAuthorIp()
     */
    public function getCommentAuthorIpAttribute(): ?string
    {
        return $this->getAttributes()[self::AUTHOR_IP] ?? null;
    }

    /**
     * @param mixed $ip
     * @return self
     * @see setCommentAuthorIp()
     */
    public function setCommentAuthorIpAttribute(mixed $ip): self
    {
        $this->attributes[self::AUTHOR_IP] = $ip;
        return $this;
    }
}
