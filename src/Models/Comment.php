<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Contracts\CommentInterface;
use Dbout\WpOrm\Contracts\CommentMetaInterface;
use Dbout\WpOrm\Contracts\PostInterface;
use Dbout\WpOrm\Contracts\UserInterface;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Comment
 * @package Dbout\WpOrm\Models
 *
 * @method static CommentInterface find(int $commentId);
 * @property UserInterface|null $user
 * @property PostInterface|null $post
 * @property CommentMetaInterface[] $metas
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
class Comment extends AbstractModel implements CommentInterface
{
    
    const CREATED_AT = self::COMMENT_DATE;
    const UPDATED_AT =  null;

    /**
     * @var string
     */
    protected $primaryKey = self::COMMENT_ID;

    /**
     * @var string
     */
    protected $table = 'comments';

    /**
     * @param string $author
     * @return CommentInterface
     */
    public function setAuthor(string $author): CommentInterface
    {
        $this->setAttribute(self::COMMENT_AUTHOR, $author);
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->getAttribute(self::COMMENT_AUTHOR);
    }

    /**
     * @param string $email
     * @return CommentInterface
     */
    public function setAuthorEmail(string $email): CommentInterface
    {
        $this->setAttribute(self::COMMENT_AUTHOR_EMAIL, $email);
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorEmail(): string
    {
        return $this->getAttribute(self::COMMENT_AUTHOR_EMAIL);
    }

    /**
     * @param string|null $url
     * @return CommentInterface
     */
    public function setAuthorUrl(?string $url): CommentInterface
    {
        $this->setAttribute(self::COMMENT_AUTHOR_URL, $url);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAuthorUrl(): ?string
    {
        return $this->getAttribute(self::COMMENT_AUTHOR_URL);
    }

    /**
     * @param string|null $ip
     * @return CommentInterface
     */
    public function setAuthorIp(?string $ip): CommentInterface
    {
        $this->setAttribute(self::COMMENT_AUTHOR_IP, $ip);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAuthorIp(): ?string
    {
        return $this->getAttribute(self::COMMENT_AUTHOR_IP);
    }

    /**
     * @param $date
     * @return CommentInterface
     */
    public function setDate($date): CommentInterface
    {
        $this->setAttribute(self::COMMENT_DATE, $date);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->getAttribute(self::COMMENT_DATE);
    }

    /**
     * @param $date
     * @return CommentInterface
     */
    public function setDateGMT($date): CommentInterface
    {
        $this->setAttribute(self::COMMENT_DATE_GMT, $date);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateGMT()
    {
        return $this->getAttribute(self::COMMENT_DATE_GMT);
    }

    /**
     * @param string $content
     * @return CommentInterface
     */
    public function setContent(string $content): CommentInterface
    {
        $this->setAttribute(self::COMMENT_CONTENT, $content);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->getAttribute(self::COMMENT_CONTENT);
    }

    /**
     * @param int $karma
     * @return CommentInterface
     */
    public function setKarma(int $karma): CommentInterface
    {
        $this->setAttribute(self::COMMENT_KARMA, $karma);
        return $this;
    }

    /**
     * @return int
     */
    public function getKarma(): int
    {
        return $this->getAttribute(self::COMMENT_KARMA);
    }

    /**
     * @param string $approved
     * @return CommentInterface
     */
    public function setApproved(string $approved): CommentInterface
    {
        $this->setAttribute(self::COMMENT_APPROVED, $approved);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getApproved()
    {
        return $this->getAttribute(self::COMMENT_APPROVED);
    }

    /**
     * @param string $agent
     * @return CommentInterface
     */
    public function setAgent(string $agent): CommentInterface
    {
        $this->setAttribute(self::COMMENT_AGENT, $agent);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAgent(): ?string
    {
        return $this->getAttribute(self::COMMENT_AGENT);
    }

    /**
     * @param string|null $type
     * @return CommentInterface
     */
    public function setType(?string $type): CommentInterface
    {
        $this->setAttribute(self::COMMENT_TYPE, $type);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->getAttribute(self::COMMENT_TYPE);
    }

    /**
     * @return HasOne
     */
    public function post(): HasOne
    {
        return $this->hasOne(Post::class, Post::POST_ID, self::COMMENT_POST_ID);
    }

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, UserInterface::USER_ID, self::USER_ID);
    }

    /**
     * @return HasMany
     */
    public function metas(): HasMany
    {
        return $this->hasMany(CommentMeta::class, CommentMetaInterface::COMMENT_ID);
    }

}