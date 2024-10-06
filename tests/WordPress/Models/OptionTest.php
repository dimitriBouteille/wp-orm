<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Models;

use Dbout\WpOrm\Models\Option;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class OptionTest extends TestCase
{
    /**
     * @return void
     * @covers Option::findOneByName
     * @covers Option::getOptionName
     * @covers Option::getOptionValue
     */
    public function testFindOneByName(): void
    {
        add_option('my_custom_option', 'option_value');
        $option = Option::findOneByName('my_custom_option');

        $this->assertInstanceOf(Option::class, $option);
        $this->assertFindLastQuery('options', 'option_value', 'my_custom_option');
        $this->assertEquals('option_value', $option->getOptionValue());
        $this->assertEquals('my_custom_option', $option->getOptionName());
    }
}
