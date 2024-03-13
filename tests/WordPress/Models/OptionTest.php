<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Models;

use Dbout\WpOrm\Models\Option;
use Dbout\WpOrm\Tests\WordPress\Helpers\WithFindOneBy;
use Dbout\WpOrm\Tests\WordPress\TestCase;

/**
 * @coversDefaultClass \Dbout\WpOrm\Models\Option
 */
class OptionTest extends TestCase
{
    use WithFindOneBy;

    private const OPTION_NAME = 'my_custom_option';
    private const OPTION_VALUE = 'option_value';

    private static ?int $optionId = null;

    /**
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        self::$optionId = self::factory()->option->create([
            'option_name' => self::OPTION_NAME,
            'option_value'  => self::OPTION_VALUE,
        ]);
    }

    /**
     * @return void
     * @covers ::findOneByName
     * @covers ::getOptionName
     * @covers ::getOptionValue
     */
    public function testFindOneByName(): void
    {
        $option = Option::findOneByName(self::OPTION_NAME);

        $this->assertInstanceOf(Option::class, $option);
        $this->checkFindOneByQuery('options', 'option_name', self::OPTION_NAME);

        $this->assertEquals(self::$optionId, $option->getId());
        $this->assertEquals(self::OPTION_VALUE, $option->getOptionValue());
        $this->assertEquals(self::OPTION_NAME, $option->getOptionName());
    }
}
