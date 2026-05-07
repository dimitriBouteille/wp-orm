<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Builders;

use Dbout\WpOrm\Builders\OptionBuilder;
use Dbout\WpOrm\Models\Option;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class OptionBuilderTest extends TestCase
{
    /**
     * @return void
     * @covers OptionBuilder::whereName
     */
    public function testWhereNameReturnsMatchingOption(): void
    {
        add_option('wp_orm_test_option', 'expected_value');
        add_option('wp_orm_other_option', 'other_value');

        /** @var Option|null $option */
        $option = Option::query()
            ->whereName('wp_orm_test_option')
            ->first();

        $this->assertNotNull($option);
        $this->assertEquals('wp_orm_test_option', $option->getOptionName());
        $this->assertEquals('expected_value', $option->getOptionValue());
    }

    /**
     * @return void
     * @covers OptionBuilder::whereName
     */
    public function testWhereNameReturnsNullWhenNoMatch(): void
    {
        add_option('wp_orm_test_option', 'expected_value');

        $option = Option::query()
            ->whereName('wp_orm_unknown_option')
            ->first();

        $this->assertNull($option);
    }

    /**
     * @return void
     * @covers OptionBuilder::whereName
     */
    public function testWhereNameReturnsExactlyOneRow(): void
    {
        add_option('wp_orm_test_option', 'expected_value');
        add_option('wp_orm_test_option_2', 'other_value');

        $options = Option::query()
            ->whereName('wp_orm_test_option')
            ->get();

        $this->assertCount(1, $options->toArray());
    }

    /**
     * @return void
     * @covers OptionBuilder::whereName
     */
    public function testWhereNameCanBeChained(): void
    {
        add_option('wp_orm_test_option', 'expected_value');

        $count = Option::query()
            ->whereName('wp_orm_test_option')
            ->where(Option::VALUE, 'expected_value')
            ->count();

        $this->assertEquals(1, $count);
    }
}
