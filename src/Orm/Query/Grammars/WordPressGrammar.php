<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Orm\Query\Grammars;

use Illuminate\Database\Query\Grammars\MySqlGrammar;

class WordPressGrammar extends MySqlGrammar
{
    /**
     * @inheritDoc
     */
    protected function wrapJsonSelector($value): string
    {
        [$field, $path] = $this->wrapJsonFieldAndPath($value);
        $path = \str_replace('"', '', $path);
        return 'json_unquote(json_extract('.$field.$path.'))';
    }
}
