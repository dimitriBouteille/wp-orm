<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Taps\Option;

use Dbout\WpOrm\Enums\YesNo;
use Dbout\WpOrm\Models\Option;
use Dbout\WpOrm\Taps\Option\IsAutoloadTap;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class IsAutoloadTapTest extends TestCase
{
    /**
     * Prefix used to isolate the test fixtures from the options
     * already loaded by the WordPress bootstrap (siteurl, blogname, …).
     */
    private const string PREFIX = 'wp_orm_autoload_test_';

    /**
     * @param string $name
     * @param string $autoload
     * @return Option
     */
    private function createOption(string $name, string $autoload): Option
    {
        $option = new Option();
        $option->setOptionName(self::PREFIX . $name);
        $option->setOptionValue('value');
        $option->setAutoload($autoload);
        $option->save();

        return $option;
    }

    /**
     * @return void
     * @covers IsAutoloadTap::__construct
     * @covers IsAutoloadTap::__invoke
     */
    public function testFiltersAutoloadedOptionsWithBoolTrue(): void
    {
        $autoloaded = $this->createOption('one', 'yes');
        $this->createOption('two', 'no');

        $options = Option::query()
            ->tap(new IsAutoloadTap(true))
            ->where(Option::NAME, 'LIKE', self::PREFIX . '%')
            ->get();

        $names = $options->pluck(Option::NAME)->toArray();

        $this->assertCount(1, $options->toArray());
        $this->assertEquals([$autoloaded->getOptionName()], $names);
    }

    /**
     * @return void
     * @covers IsAutoloadTap::__invoke
     */
    public function testFiltersNonAutoloadedOptionsWithBoolFalse(): void
    {
        $this->createOption('one', 'yes');
        $manual = $this->createOption('two', 'no');

        $options = Option::query()
            ->tap(new IsAutoloadTap(false))
            ->where(Option::NAME, 'LIKE', self::PREFIX . '%')
            ->get();

        $names = $options->pluck(Option::NAME)->toArray();

        $this->assertCount(1, $options->toArray());
        $this->assertEquals([$manual->getOptionName()], $names);
    }

    /**
     * @return void
     * @covers IsAutoloadTap::__construct
     */
    public function testDefaultsToAutoloadedWhenNoParameterProvided(): void
    {
        $autoloaded = $this->createOption('one', 'yes');
        $this->createOption('two', 'no');

        $options = Option::query()
            ->tap(new IsAutoloadTap())
            ->where(Option::NAME, 'LIKE', self::PREFIX . '%')
            ->get();

        $names = $options->pluck(Option::NAME)->toArray();

        $this->assertCount(1, $options->toArray());
        $this->assertEquals([$autoloaded->getOptionName()], $names);
    }

    /**
     * @return void
     * @covers IsAutoloadTap::__invoke
     */
    public function testAcceptsYesNoEnumYes(): void
    {
        $autoloaded = $this->createOption('one', 'yes');
        $this->createOption('two', 'no');

        $options = Option::query()
            ->tap(new IsAutoloadTap(YesNo::Yes))
            ->where(Option::NAME, 'LIKE', self::PREFIX . '%')
            ->get();

        $names = $options->pluck(Option::NAME)->toArray();

        $this->assertCount(1, $options->toArray());
        $this->assertEquals([$autoloaded->getOptionName()], $names);
    }

    /**
     * @return void
     * @covers IsAutoloadTap::__invoke
     */
    public function testAcceptsYesNoEnumNo(): void
    {
        $this->createOption('one', 'yes');
        $manual = $this->createOption('two', 'no');

        $options = Option::query()
            ->tap(new IsAutoloadTap(YesNo::No))
            ->where(Option::NAME, 'LIKE', self::PREFIX . '%')
            ->get();

        $names = $options->pluck(Option::NAME)->toArray();

        $this->assertCount(1, $options->toArray());
        $this->assertEquals([$manual->getOptionName()], $names);
    }

    /**
     * @return void
     * @covers IsAutoloadTap::__invoke
     */
    public function testReturnsEmptyWhenNoOptionsMatch(): void
    {
        $this->createOption('one', 'yes');

        $options = Option::query()
            ->tap(new IsAutoloadTap(false))
            ->where(Option::NAME, 'LIKE', self::PREFIX . '%')
            ->get();

        $this->assertCount(0, $options->toArray());
    }

    /**
     * @return void
     * @covers IsAutoloadTap::__invoke
     */
    public function testCanBeChainedWithWhereName(): void
    {
        $expected = $this->createOption('expected', 'yes');
        $this->createOption('other', 'yes');
        $this->createOption('expected_2', 'no');

        /** @var Option|null $option */
        $option = Option::query()
            ->tap(new IsAutoloadTap(true))
            ->whereName(self::PREFIX . 'expected')
            ->first();

        $this->assertNotNull($option);
        $this->assertEquals($expected->getId(), $option->getId());
    }
}
