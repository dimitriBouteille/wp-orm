<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Carbon\Carbon;
use Dbout\WpOrm\Api\CommentInterface;
use Dbout\WpOrm\Api\PostInterface;
use Dbout\WpOrm\Api\UserInterface;
use Dbout\WpOrm\Builders\CommentBuilder;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @method static static|null find(int $commentId)
 * @method static CommentBuilder query()
 */
class Comment extends AbstractModel implements CommentInterface
{
    final public const CREATED_AT = self::DATE;
    final public const UPDATED_AT =  null;

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
    protected $guarded = [];

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
        return $this->hasOne(User::class, UserInterface::USER_ID, self::USER_ID);
    }

    /**
     * @return HasOne
     */
    public function post(): HasOne
    {
        return $this->hasOne(Post::class, PostInterface::POST_ID, self::POST_ID);
    }

    /**
     * @return HasOne
     */
    public function parent(): HasOne
    {
        return $this->hasOne(Comment::class, CommentInterface::COMMENT_ID, self::PARENT);
    }

    /**
     * @inheritDoc
     */
    public function newEloquentBuilder($query): CommentBuilder
    {
        return new CommentBuilder($query);
    }

    /**
     * @inheritDoc
     */
    public function setAuthor(string $author): CommentInterface
    {
        return $this->setCommentAuthor($author);
    }

    /**
     * @inheritDoc
     */
    public function getAuthor(): ?string
    {
        return $this->getCommentAuthor();
    }

    /**
     * @inheritDoc
     */
    public function setAuthorEmail(?string $email): CommentInterface
    {
        return $this->setCommentAuthorEmail($email);
    }

    /**
     * @inheritDoc
     */
    public function getAuthorEmail(): ?string
    {
        return $this->getCommentAuthorEmail();
    }

    /**
     * @inheritDoc
     */
    public function setAuthorUrl(?string $url): CommentInterface
    {
        return $this->setCommentAuthorUrl($url);
    }

    /**
     * @inheritDoc
     */
    public function getAuthorUrl(): ?string
    {
        return $this->getCommentAuthorUrl();
    }

    /**
     * @inheritDoc
     */
    public function setAuthorIp(?string $ip): CommentInterface
    {
        return $this->setCommentAuthorIP($ip);
    }

    /**
     * @inheritDoc
     */
    public function getAuthorIp(): ?string
    {
        return $this->getCommentAuthorIP();
    }

    /**
     * @inheritDoc
     */
    public function setContent(?string $content): CommentInterface
    {
        return $this->setCommentContent($content);
    }

    /**
     * @inheritDoc
     */
    public function getContent(): ?string
    {
        return $this->getCommentContent();
    }

    /**
     * @inheritDoc
     */
    public function setKarma(?int $karma): CommentInterface
    {
        return $this->setCommentKarma($karma);
    }

    /**
     * @inheritDoc
     */
    public function getKarma(): ?int
    {
        return $this->getCommentKarma();
    }

    /**
     * @inheritDoc
     */
    public function setAgent(?string $agent): CommentInterface
    {
        return $this->setCommentAgent($agent);
    }

    /**
     * @inheritDoc
     */
    public function getAgent(): ?string
    {
        return $this->getCommentAgent();
    }

    /**
     * @inheritDoc
     */
    public function setType(?string $type): CommentInterface
    {
        return $this->setCommentType($type);
    }

    /**
     * @inheritDoc
     */
    public function getType(): ?string
    {
        return $this->getCommentType();
    }

    /**
     * @inheritDoc
     */
    public function setApproved(?string $approved): CommentInterface
    {
        return $this->setCommentApproved($approved);
    }

    /**
     * @inheritDoc
     */
    public function getApproved(): ?string
    {
        return $this->getCommentApproved();
    }

    /**
     * @inheritDoc
     */
    public function setDate(mixed $date): CommentInterface
    {
        return $this->setCommentDate($date);
    }

    /**
     * @inheritDoc
     */
    public function getDate(): ?Carbon
    {
        return $this->getCommentDate();
    }

    /**
     * @inheritDoc
     */
    public function setDateGMT(mixed $date): CommentInterface
    {
        return $this->setCommentDateGmt($date);
    }

    /**
     * @inheritDoc
     */
    public function getDateGMT(): ?Carbon
    {
        return $this->getCommentDateGmt();
    }
}
