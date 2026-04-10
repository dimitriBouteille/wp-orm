<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Casts;

use Dbout\WpOrm\Models\Option;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class WpSerializedCastTest extends TestCase
{
    /**
     * @return void
     * @covers WpSerializedCast::get
     * @covers WpSerializedCast::set
     */
    public function testSaveArrayAndReadBack(): void
    {
        $value = ['key1' => 'value1', 'key2' => 'value2'];

        $option = new Option();
        $option->setOptionName('test_array_option');
        $option->setOptionValue($value);
        $this->assertTrue($option->save());

        $loaded = Option::find($option->getId());
        $this->assertIsArray($loaded->getOptionValue());
        $this->assertSame($value, $loaded->getOptionValue());
    }

    /**
     * @return void
     * @covers WpSerializedCast::get
     * @covers WpSerializedCast::set
     */
    public function testSaveNestedArrayAndReadBack(): void
    {
        $value = [
            'level1' => [
                'level2' => [
                    'level3' => 'deep',
                ],
            ],
            'list' => [1, 2, 3],
        ];

        $option = new Option();
        $option->setOptionName('test_nested_array_option');
        $option->setOptionValue($value);
        $this->assertTrue($option->save());

        $loaded = Option::find($option->getId());
        $this->assertSame($value, $loaded->getOptionValue());
    }

    /**
     * @return void
     * @covers WpSerializedCast::get
     * @covers WpSerializedCast::set
     */
    public function testSaveStringAndReadBack(): void
    {
        $option = new Option();
        $option->setOptionName('test_string_option');
        $option->setOptionValue('simple string');
        $this->assertTrue($option->save());

        $loaded = Option::find($option->getId());
        $this->assertSame('simple string', $loaded->getOptionValue());
    }

    /**
     * @return void
     * @covers WpSerializedCast::get
     * @covers WpSerializedCast::set
     */
    public function testSaveNumericStringAndReadBack(): void
    {
        $option = new Option();
        $option->setOptionName('test_numeric_option');
        $option->setOptionValue('42');
        $this->assertTrue($option->save());

        $loaded = Option::find($option->getId());
        $this->assertSame('42', $loaded->getOptionValue());
    }

    /**
     * @return void
     * @covers WpSerializedCast::get
     * @covers WpSerializedCast::set
     */
    public function testSaveEmptyArrayAndReadBack(): void
    {
        $option = new Option();
        $option->setOptionName('test_empty_array_option');
        $option->setOptionValue([]);
        $this->assertTrue($option->save());

        $loaded = Option::find($option->getId());
        $this->assertSame([], $loaded->getOptionValue());
    }

    /**
     * @return void
     * @covers WpSerializedCast::set
     */
    public function testDatabaseContainsSerializedValue(): void
    {
        global $wpdb;

        $value = ['foo' => 'bar', 'baz' => [1, 2, 3]];

        $option = new Option();
        $option->setOptionName('test_raw_serialized');
        $option->setOptionValue($value);
        $this->assertTrue($option->save());

        $raw = $wpdb->get_var($wpdb->prepare(
            "SELECT option_value FROM {$wpdb->options} WHERE option_id = %d",
            $option->getId()
        ));

        $this->assertSame(serialize($value), $raw);
    }

    /**
     * @return void
     * @covers WpSerializedCast::set
     */
    public function testDatabaseContainsPlainStringForScalar(): void
    {
        global $wpdb;

        $option = new Option();
        $option->setOptionName('test_raw_string');
        $option->setOptionValue('hello world');
        $this->assertTrue($option->save());

        $raw = $wpdb->get_var($wpdb->prepare(
            "SELECT option_value FROM {$wpdb->options} WHERE option_id = %d",
            $option->getId()
        ));

        $this->assertSame('hello world', $raw);
    }

    /**
     * @return void
     * @covers WpSerializedCast::get
     * @covers WpSerializedCast::maybeUnserialize
     */
    public function testReadOptionCreatedByWordPress(): void
    {
        $value = ['wp_key' => 'wp_value', 'nested' => ['a', 'b']];
        add_option('wp_native_option', $value);

        $option = Option::findOneByName('wp_native_option');
        $this->assertNotNull($option);
        $this->assertSame($value, $option->getOptionValue());
    }

    /**
     * @return void
     * @covers WpSerializedCast::get
     * @covers WpSerializedCast::maybeUnserialize
     */
    public function testReadScalarOptionCreatedByWordPress(): void
    {
        add_option('wp_scalar_option', 'just a string');

        $option = Option::findOneByName('wp_scalar_option');
        $this->assertNotNull($option);
        $this->assertSame('just a string', $option->getOptionValue());
    }

    /**
     * @return void
     * @covers WpSerializedCast::set
     */
    public function testWordPressCanReadOptionSavedByModel(): void
    {
        $value = ['model_key' => 'model_value', 'items' => [10, 20, 30]];

        $option = new Option();
        $option->setOptionName('model_saved_option');
        $option->setOptionValue($value);
        $this->assertTrue($option->save());

        $wpValue = get_option('model_saved_option');
        $this->assertSame($value, $wpValue);
    }

    /**
     * @return void
     * @covers WpSerializedCast::set
     */
    public function testWordPressCanReadScalarOptionSavedByModel(): void
    {
        $option = new Option();
        $option->setOptionName('model_saved_scalar');
        $option->setOptionValue('scalar value');
        $this->assertTrue($option->save());

        $wpValue = get_option('model_saved_scalar');
        $this->assertSame('scalar value', $wpValue);
    }

    /**
     * @return void
     * @covers WpSerializedCast::get
     * @covers WpSerializedCast::set
     */
    public function testUpdateArrayOptionValue(): void
    {
        $option = new Option();
        $option->setOptionName('test_update_option');
        $option->setOptionValue(['initial' => true]);
        $this->assertTrue($option->save());

        $loaded = Option::find($option->getId());
        $loaded->setOptionValue(['updated' => true, 'extra' => 'data']);
        $this->assertTrue($loaded->save());

        $reloaded = Option::find($option->getId());
        $this->assertSame(['updated' => true, 'extra' => 'data'], $reloaded->getOptionValue());
    }

    /**
     * @return void
     * @covers WpSerializedCast::get
     * @covers WpSerializedCast::set
     */
    public function testUpdateFromScalarToArray(): void
    {
        $option = new Option();
        $option->setOptionName('test_scalar_to_array');
        $option->setOptionValue('initial');
        $this->assertTrue($option->save());

        $loaded = Option::find($option->getId());
        $loaded->setOptionValue(['now' => 'an array']);
        $this->assertTrue($loaded->save());

        $reloaded = Option::find($option->getId());
        $this->assertSame(['now' => 'an array'], $reloaded->getOptionValue());
    }

    /**
     * @return void
     * @covers WpSerializedCast::get
     * @covers WpSerializedCast::set
     */
    public function testUpdateFromArrayToScalar(): void
    {
        $option = new Option();
        $option->setOptionName('test_array_to_scalar');
        $option->setOptionValue(['was' => 'array']);
        $this->assertTrue($option->save());

        $loaded = Option::find($option->getId());
        $loaded->setOptionValue('now a string');
        $this->assertTrue($loaded->save());

        $reloaded = Option::find($option->getId());
        $this->assertSame('now a string', $reloaded->getOptionValue());
    }
}
