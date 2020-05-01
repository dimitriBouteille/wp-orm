<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Contracts\CommentInterface;
use Dbout\WpOrm\Contracts\CommentMetaInterface;
use Dbout\WpOrm\Contracts\MetaInterface;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class CommentMeta
 * @package Dbout\WpOrm\Models
 *
 * @method static CommentMetaInterface find(int $metaId);
 * @property CommentInterface|null $comment
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
class CommentMeta extends AbstractModel implements CommentMetaInterface
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
     * Disable created_at and updated_at
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return HasOne
     */
    public function comment(): HasOne
    {
        return $this->hasOne(Comment::class, Comment::USER_ID, self::COMMENT_ID);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->getAttribute(self::META_KEY);
    }

    /**
     * @param string $key
     * @return MetaInterface
     */
    public function setKey(string $key): MetaInterface
    {
        $this->setAttribute(self::META_KEY, $key);
        return $this;
    }

    /**
     * @return mixed|void
     */
    public function getValue()
    {
        return $this->getAttribute(self::META_VALUE);
    }

    /**
     * @param string $value
     * @return MetaInterface
     */
    public function setValue(string $value): MetaInterface
    {
        $this->setAttribute(self::META_VALUE, $value);
        return $this;
    }

}