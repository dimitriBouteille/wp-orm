<?php

namespace Dbout\WpOrm\Models;

use Dbout\WpOrm\Contracts\CommentInterface;
use Dbout\WpOrm\Orm\AbstractModel;

/**
 * Class Comment
 * @package Dbout\WpOrm\Models
 *
 * @method static CommentInterface find(int $commentId);
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
class Comment extends AbstractModel implements CommentInterface
{

    /**
     * @var string
     */
    protected $primaryKey = self::COMMENT_ID;

    /**
     * @var string
     */
    protected $table = 'comments';

}