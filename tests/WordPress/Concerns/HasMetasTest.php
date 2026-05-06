<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Concerns;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Dbout\WpOrm\Concerns\HasMetas;
use Dbout\WpOrm\Enums\YesNo;
use Dbout\WpOrm\Models\Meta\AbstractMeta;
use Dbout\WpOrm\Models\Meta\PostMeta;
use Dbout\WpOrm\Models\Post;
use Dbout\WpOrm\Tests\WordPress\Support\BuildsTestPost;
use Dbout\WpOrm\Tests\WordPress\TestCase;
use Illuminate\Events\Dispatcher;

class HasMetasTest extends TestCase
{
    use BuildsTestPost;

    /**
     * @return void
     * @covers HasMetas::getMeta
     * @uses Post
     */
    public function testGetMeta(): void
    {
        $model = $this->aPost(__FUNCTION__);
        $createMeta = $model->setMeta('author', 'Norman FOSTER');

        $meta = $model->getMeta('author');
        $this->assertInstanceOf(AbstractMeta::class, $meta);
        $this->assertEquals($createMeta->getId(), $meta->getId());
        $this->assertEquals($createMeta->getValue(), $meta->getValue());
        $this->assertEquals('Norman FOSTER', $meta->getValue());
        $this->assertEquals('author', $meta->getMetaKey());
    }

    /**
     * @return void
     * @covers AbstractMeta::getMetaKey
     * @covers AbstractMeta::setMetaKey
     * @uses Post
     */
    public function testGetAndSetMetaKey(): void
    {
        $model = $this->aPostWithMetas(['author' => 'Norman FOSTER'], __FUNCTION__);

        $meta = $model->getMeta('author');
        $this->assertInstanceOf(AbstractMeta::class, $meta);
        $this->assertEquals('author', $meta->getMetaKey());

        $meta->setMetaKey('renamed_author');
        $this->assertEquals('renamed_author', $meta->getMetaKey());
    }

    /**
     * @return void
     * @covers AbstractMeta::getKey
     * @covers AbstractMeta::setKey
     * @uses Post
     */
    public function testDeprecatedGetAndSetKeyStillWork(): void
    {
        $model = $this->aPostWithMetas(['author' => 'Norman FOSTER'], __FUNCTION__);

        $meta = $model->getMeta('author');
        $this->assertInstanceOf(AbstractMeta::class, $meta);

        // Deprecated API must keep returning the meta key for BC.
        $this->assertEquals('author', $meta->getKey());

        $meta->setKey('renamed_author');
        $this->assertEquals('renamed_author', $meta->getKey());
        $this->assertEquals('renamed_author', $meta->getMetaKey());
    }

    /**
     * @return void
     * @covers HasMetas::setMeta
     * @uses Post
     */
    public function testSetMeta(): void
    {
        $model = $this->aPost(__FUNCTION__);
        $meta = $model->setMeta('build-by', 'John D.');

        $this->assertEquals('John D.', get_post_meta($model->getId(), 'build-by', true));
        $this->assertInstanceOf(PostMeta::class, $meta);

        $loadedMeta = $model->getMeta('build-by');
        $this->assertEquals($meta->getId(), $loadedMeta->getId());
    }

    /**
     * @return void
     * @covers HasMetas::hasMeta
     * @uses Post
     */
    public function testHasMeta(): void
    {
        $model = $this->aPostWithMetas(['birthday-date' => '17/09/1900'], __FUNCTION__);
        $this->assertTrue($model->hasMeta('birthday-date'));

        $wpMetaId = add_post_meta($model->getId(), 'birthday-place', 'France');
        $this->assertTrue($model->hasMeta('birthday-place'));
        $this->assertEquals('France', $model->getMetaValue('birthday-place'));
        $this->assertEquals($wpMetaId, $model->getMeta('birthday-place')?->getId());
    }

    /**
     * @return void
     * @covers HasMetas::getMetaValue
     * @uses Post
     */
    public function testGetMetaValueWithoutCast(): void
    {
        $model = $this->aPostWithMetas(['build-by' => 'John D.'], __FUNCTION__);

        add_post_meta($model->getId(), 'place', 'Lyon, France');
        $this->assertEquals('Lyon, France', $model->getMetaValue('place'));
    }

    /**
     * @param string $castType
     * @param string $stored
     * @param mixed $expected
     * @return void
     * @covers HasMetas::getMetaValue
     * @dataProvider providerGenericCasts
     * @uses Post
     */
    public function testGetMetaValueWithGenericCast(string $castType, string $stored, mixed $expected): void
    {
        $model = $this->aPostWithMetaCasts(['value' => $castType], ['value' => $stored]);
        $this->assertEquals($expected, $model->getMetaValue('value'));
    }

    /**
     * @return \Generator<string, array{string, string, mixed}>
     */
    public static function providerGenericCasts(): \Generator
    {
        yield 'int'        => ['int',     '18',   18];
        yield 'integer'    => ['integer', '2024', 2024];
        yield 'bool true'  => ['bool',    '1',    true];
        yield 'bool false' => ['boolean', '0',    false];
        yield 'json'       => [
            'json',
            '{"firstname":"John","lastname":"Doe"}',
            ['firstname' => 'John', 'lastname' => 'Doe'],
        ];
    }

    /**
     * @return void
     * @covers HasMetas::getMetaValue
     * @uses Post
     */
    public function testGetMetaValueWithEnumCasts(): void
    {
        $model = $this->aPostWithMetaCasts(
            ['active' => YesNo::class],
            ['active' => 'yes'],
        );

        /** @var YesNo $value */
        $value = $model->getMetaValue('active');

        $this->assertInstanceOf(YesNo::class, $value);
        $this->assertEquals('yes', $value->value);
    }

    /**
     * @param string $castType
     * @param string $stored
     * @param string $expectedFormatted
     * @param class-string $expectedClass
     * @return void
     * @covers HasMetas::getMetaValue
     * @dataProvider providerDatetimeCasts
     * @uses Post
     */
    public function testGetMetaValueWithDatetimeCast(
        string $castType,
        string $stored,
        string $expectedFormatted,
        string $expectedClass
    ): void {
        $model = $this->aPostWithMetaCasts(['value' => $castType], ['value' => $stored]);

        $value = $model->getMetaValue('value');
        $this->assertInstanceOf($expectedClass, $value);
        $this->assertEquals($expectedFormatted, $value->format('Y-m-d H:i:s'));
    }

    /**
     * @return \Generator<string, array{string, string, string, class-string}>
     */
    public static function providerDatetimeCasts(): \Generator
    {
        yield 'datetime' => [
            'datetime',
            '2022-09-08 07:30:05',
            '2022-09-08 07:30:05',
            Carbon::class,
        ];
        yield 'date strips time' => [
            'date',
            '2024-10-08 10:25:35',
            '2024-10-08 00:00:00',
            Carbon::class,
        ];
        yield 'datetime with custom format' => [
            'datetime:Y-m-d',
            '2024-03-12 14:25:00',
            '2024-03-12 14:25:00',
            Carbon::class,
        ];
        yield 'immutable_datetime' => [
            'immutable_datetime:Y-m-d H:i:s',
            '2024-04-01 09:00:00',
            '2024-04-01 09:00:00',
            CarbonImmutable::class,
        ];
    }

    /**
     * @param string $castType
     * @param string $stored
     * @param string $expected
     * @return void
     * @covers HasMetas::getMetaValue
     * @dataProvider providerDecimalCasts
     * @uses Post
     */
    public function testGetMetaValueWithDecimalCast(string $castType, string $stored, string $expected): void
    {
        $model = $this->aPostWithMetaCasts(['value' => $castType], ['value' => $stored]);
        $this->assertSame($expected, $model->getMetaValue('value'));
    }

    /**
     * @return \Generator<string, array{string, string, string}>
     */
    public static function providerDecimalCasts(): \Generator
    {
        yield 'decimal:2 rounds' => ['decimal:2', '12.3456', '12.35'];
        yield 'decimal:4 pads'   => ['decimal:4', '0.5',     '0.5000'];
    }

    /**
     * @return void
     * @covers HasMetas::getMetaValue
     * @uses Post
     */
    public function testGetMetaValueWithInvalidCasts(): void
    {
        $model = $this->aPostWithMetaCasts(['my_meta' => 'boolean_'], ['my_meta' => 'yes']);
        $this->assertEquals('yes', $model->getMetaValue('my_meta'));
    }

    /**
     * @return void
     * @covers HasMetas::deleteMeta
     * @uses Post
     */
    public function testDeleteMeta(): void
    {
        $model = $this->aPostWithMetas(['architect-name' => 'Norman F.'], __FUNCTION__);

        $this->assertEquals(1, $model->deleteMeta('architect-name'), 'The function must delete only one line.');
        $this->assertFalse($model->hasMeta('architect-name'), 'The meta must no longer exist.');
    }

    /**
     * @return void
     * @covers HasMetas::deleteMeta
     * @uses Post
     */
    public function testDeleteUndefinedMeta(): void
    {
        $model = $this->aPostWithMetas(['architect-name' => 'Norman F.'], __FUNCTION__);

        $this->assertEquals(0, $model->deleteMeta('fake-meta'));
        $this->assertTrue($model->hasMeta('architect-name'), 'The unrelated meta must still exist.');
    }

    /**
     * @return void
     * @covers HasMetas::setMeta
     * @covers HasMetas::hasMeta
     * @covers HasMetas::getMeta
     * @uses Post
     */
    public function testSaveNewModelWithMetas(): void
    {
        $metaValue = '31-10-1950';
        $metaKey = 'birthday-date';

        $object = new class () extends Post {
            protected static function boot()
            {
                static::setEventDispatcher(new Dispatcher());
                parent::boot();
            }
        };

        $model = new $object();
        $model->setPostTitle('Zaha Hadid projects');
        $model->setMeta($metaKey, $metaValue);
        $model->save();

        $this->assertEquals($metaValue, get_post_meta($model->getId(), $metaKey, true));
        $this->assertInstanceOf(PostMeta::class, $model->getMeta($metaKey));
        $this->assertTrue($model->hasMeta($metaKey));
    }
}
