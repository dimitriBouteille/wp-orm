<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Dbout\WpOrm\Orm\Schemas;

use Illuminate\Database\Schema\Grammars\MySqlGrammar;
use Illuminate\Database\Schema\MySqlBuilder;

class WordPressBuilder extends MySqlBuilder
{
    /**
     * @var MySqlGrammar
     */
    protected $grammar;
}
