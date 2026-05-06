<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
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
        $this->assertEquals('option_value', $option->getOptionValue());
        $this->assertEquals('my_custom_option', $option->getOptionName());
    }

    /**
     * @return void
     * @covers Option::findOneByName
     */
    public function testFindOneByNameWithNotFound(): void
    {
        add_option('my_custom_option', 'option_value');
        $option = Option::findOneByName('my_custom_option_fake');
        $this->assertNull($option);
    }

    /**
     * @return void
     * @covers Option::save
     * @covers Option::setOptionName
     * @covers Option::setOptionValue
     * @covers Option::getOptionValue
     * @covers Option::getOptionName
     * @covers Option::getId
     */
    public function testSave(): void
    {
        $option = new Option();
        $option->setOptionName('my_custom_option');
        $option->setOptionValue('option_value');

        $this->assertTrue($option->save());

        $loadedObject = Option::find($option->getId());
        $this->assertInstanceOf(Option::class, $loadedObject);
        $this->assertEquals('option_value', $loadedObject->getOptionValue());
        $this->assertEquals('my_custom_option', $loadedObject->getOptionName());
        $this->assertEquals($option->getId(), $loadedObject->getId());
    }
}
