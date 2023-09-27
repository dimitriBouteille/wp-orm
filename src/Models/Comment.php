<?php
/**
 * Copyright (c) 2023 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Api\CommentInterface;
use Dbout\WpOrm\Api\PostInterface;
use Dbout\WpOrm\Api\UserInterface;
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
    protected $casts = [
        self::KARMA => 'integer',
        self::DATE_GMT => 'date',
        self::DATE => 'date',
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
}
