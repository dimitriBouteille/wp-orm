<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\Unit\Casts;

use Dbout\WpOrm\Casts\WpSerializedCast;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(WpSerializedCast::class)]
class WpSerializedCastTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetWithNullValue(): void
    {
        $cast = new WpSerializedCast();
        $this->assertNull($cast->get($this->createMockModel(), 'key', null, []));
    }

    /**
     * @return void
     */
    public function testGetWithPlainString(): void
    {
        $cast = new WpSerializedCast();
        $this->assertSame('hello world', $cast->get($this->createMockModel(), 'key', 'hello world', []));
    }

    /**
     * @return void
     */
    public function testGetWithNumericString(): void
    {
        $cast = new WpSerializedCast();
        $this->assertSame('42', $cast->get($this->createMockModel(), 'key', '42', []));
    }

    /**
     * @return void
     */
    public function testGetWithSerializedArray(): void
    {
        $original = ['key1' => 'value1', 'key2' => 'value2'];
        $serialized = serialize($original);

        $cast = new WpSerializedCast();
        $result = $cast->get($this->createMockModel(), 'key', $serialized, []);

        $this->assertSame($original, $result);
    }

    /**
     * @return void
     */
    public function testGetWithSerializedNestedArray(): void
    {
        $original = ['level1' => ['level2' => ['level3' => 'deep']]];
        $serialized = serialize($original);

        $cast = new WpSerializedCast();
        $result = $cast->get($this->createMockModel(), 'key', $serialized, []);

        $this->assertSame($original, $result);
    }

    /**
     * @return void
     */
    public function testGetWithSerializedBoolean(): void
    {
        $cast = new WpSerializedCast();

        $this->assertFalse($cast->get($this->createMockModel(), 'key', 'b:0;', []));
        $this->assertTrue($cast->get($this->createMockModel(), 'key', 'b:1;', []));
    }

    /**
     * @return void
     */
    public function testGetWithSerializedInteger(): void
    {
        $cast = new WpSerializedCast();
        $this->assertSame(123, $cast->get($this->createMockModel(), 'key', 'i:123;', []));
    }

    /**
     * @return void
     */
    public function testGetWithSerializedString(): void
    {
        $cast = new WpSerializedCast();
        $this->assertSame('hello', $cast->get($this->createMockModel(), 'key', 's:5:"hello";', []));
    }

    /**
     * @return void
     */
    public function testGetWithSerializedNull(): void
    {
        $cast = new WpSerializedCast();
        $this->assertNull($cast->get($this->createMockModel(), 'key', 'N;', []));
    }

    /**
     * @return void
     */
    public function testGetWithSerializedDouble(): void
    {
        $cast = new WpSerializedCast();
        $this->assertSame(3.14, $cast->get($this->createMockModel(), 'key', 'd:3.14;', []));
    }

    /**
     * @return void
     */
    public function testSetWithArray(): void
    {
        $cast = new WpSerializedCast();
        $value = ['foo' => 'bar', 'baz' => [1, 2, 3]];

        $result = $cast->set($this->createMockModel(), 'key', $value, []);

        $this->assertSame(serialize($value), $result);
    }

    /**
     * @return void
     */
    public function testSetWithScalarValue(): void
    {
        $cast = new WpSerializedCast();

        $this->assertSame('hello', $cast->set($this->createMockModel(), 'key', 'hello', []));
        $this->assertSame(42, $cast->set($this->createMockModel(), 'key', 42, []));
        $this->assertTrue($cast->set($this->createMockModel(), 'key', true, []));
        $this->assertNull($cast->set($this->createMockModel(), 'key', null, []));
    }

    /**
     * @return void
     */
    public function testSetWithObject(): void
    {
        $cast = new WpSerializedCast();
        $value = (object) ['foo' => 'bar'];

        $result = $cast->set($this->createMockModel(), 'key', $value, []);

        $this->assertIsString($result);
        $this->assertTrue(WpSerializedCast::isSerialized($result));
    }

    /**
     * @return void
     */
    public function testRoundTrip(): void
    {
        $cast = new WpSerializedCast();
        $model = $this->createMockModel();
        $original = ['option1' => true, 'option2' => [1, 2, 3], 'option3' => 'text'];

        $stored = $cast->set($model, 'key', $original, []);
        $restored = $cast->get($model, 'key', $stored, []);

        $this->assertSame($original, $restored);
    }

    /**
     * @param string $data
     * @param bool $expected
     * @return void
     */
    #[DataProvider('isSerializedProvider')]
    public function testIsSerialized(string $data, bool $expected): void
    {
        $this->assertSame($expected, WpSerializedCast::isSerialized($data));
    }

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function isSerializedProvider(): iterable
    {
        yield 'serialized boolean false' => ['b:0;', true];
        yield 'serialized boolean true' => ['b:1;', true];
        yield 'serialized null' => ['N;', true];
        yield 'serialized integer' => ['i:42;', true];
        yield 'serialized double' => ['d:3.14;', true];
        yield 'serialized string' => ['s:5:"hello";', true];
        yield 'serialized empty array' => ['a:0:{}', true];
        yield 'serialized array' => [serialize(['a' => 'b']), true];
        yield 'plain string' => ['hello world', false];
        yield 'empty string' => ['', false];
        yield 'numeric string' => ['42', false];
        yield 'short string' => ['ab', false];
        yield 'url' => ['https://example.com', false];
        yield 'json' => ['{"key":"value"}', false];
    }

    /**
     * @return void
     */
    public function testObjectsDeserializedWithoutClasses(): void
    {
        $serialized = 'O:8:"stdClass":1:{s:3:"foo";s:3:"bar";}';
        $cast = new WpSerializedCast();

        $result = $cast->get($this->createMockModel(), 'key', $serialized, []);

        // Objects should be deserialized as __PHP_Incomplete_Class (allowed_classes: false)
        $this->assertNotInstanceOf(\stdClass::class, $result);
    }

    private function createMockModel(): Model
    {
        return $this->createStub(Model::class);
    }
}
