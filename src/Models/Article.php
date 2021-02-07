<?php

namespace Dbout\WpOrm\Models;

/**
 * Class Article
 * @package Dbout\WpOrm\Models
 */
class Article extends CustomPost
{

    /**
     * @var string
     */
    protected string $_type = 'post';
}