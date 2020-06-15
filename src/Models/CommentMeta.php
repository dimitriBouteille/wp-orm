<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Contracts\CommentInterface;
use Dbout\WpOrm\Contracts\CommentMetaInterface;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class CommentMeta
 * @package Dbout\WpOrm\Models
 *
 * @method static CommentMetaInterface  find(int $metaId);
 * @property CommentInterface|null      $comment
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
class CommentMeta extends AbstractMeta implements CommentMetaInterface
{

    /**
     * @var string
     */
    protected $table = 'commentmeta';

    /**
     * @var string
     */
    protected $primaryKey = self::META_ID;

    /**
     * @return HasOne
     */
    public function comment(): HasOne
    {
        return $this->hasOne(Comment::class, Comment::USER_ID, self::COMMENT_ID);
    }
}
