<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Carbon\Carbon;
use Dbout\WpOrm\Api\CommentInterface;
use Dbout\WpOrm\Builders\CommentBuilder;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @method static Comment|null find(int $commentId)
 * @method static CommentBuilder query()
 * @property User|null $user
 * @property Post|null $post
 * @property Comment|null $parent
 */
class Comment extends AbstractModel implements CommentInterface
{
    public const CREATED_AT = self::DATE;
    public const UPDATED_AT =  null;

    /**
     * @inheritDoc
     */
    protected $primaryKey = self::COMMENT_ID;

    /**
     * @inheritDoc
     */
    protected $table = 'comments';

    /**
     * @param string $author
     * @return $this
     */
    public function setAuthor(string $author): self
    {
        $this->setAttribute(self::AUTHOR, $author);
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->getAttribute(self::AUTHOR);
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setAuthorEmail(string $email): self
    {
        $this->setAttribute(self::AUTHOR_EMAIL, $email);
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorEmail(): string
    {
        return $this->getAttribute(self::AUTHOR_EMAIL);
    }

    /**
     * @param string|null $url
     * @return $this
     */
    public function setAuthorUrl(?string $url): self
    {
        $this->setAttribute(self::AUTHOR_URL, $url);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAuthorUrl(): ?string
    {
        return $this->getAttribute(self::AUTHOR_URL);
    }

    /**
     * @param string|null $ip
     * @return $this
     */
    public function setAuthorIp(?string $ip): self
    {
        $this->setAttribute(self::AUTHOR_IP, $ip);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAuthorIp(): ?string
    {
        return $this->getAttribute(self::AUTHOR_IP);
    }

    /**
     * @param $date
     * @return $this
     */
    public function setDate($date): self
    {
        $this->setAttribute(self::DATE, $date);
        return $this;
    }

    /**
     * @return Carbon|null
     */
    public function getDate(): ?Carbon
    {
        return $this->getAttribute(self::DATE);
    }

    /**
     * @param $date
     * @return $this
     */
    public function setDateGMT($date): self
    {
        $this->setAttribute(self::DATE_GMT, $date);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateGMT()
    {
        return $this->getAttribute(self::DATE_GMT);
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): self
    {
        $this->setAttribute(self::CONTENT, $content);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->getAttribute(self::CONTENT);
    }

    /**
     * @param int $karma
     * @return $this
     */
    public function setKarma(int $karma): self
    {
        $this->setAttribute(self::KARMA, $karma);
        return $this;
    }

    /**
     * @return int
     */
    public function getKarma(): int
    {
        return $this->getAttribute(self::KARMA);
    }

    /**
     * @param string $approved
     * @return $this
     */
    public function setApproved(string $approved): self
    {
        $this->setAttribute(self::APPROVED, $approved);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getApproved()
    {
        return $this->getAttribute(self::APPROVED);
    }

    /**
     * @param string $agent
     * @return $this
     */
    public function setAgent(string $agent): self
    {
        $this->setAttribute(self::AGENT, $agent);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAgent(): ?string
    {
        return $this->getAttribute(self::AGENT);
    }

    /**
     * @param string|null $type
     * @return $this
     */
    public function setType(?string $type): self
    {
        $this->setAttribute(self::TYPE, $type);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->getAttribute(self::TYPE);
    }

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
}
