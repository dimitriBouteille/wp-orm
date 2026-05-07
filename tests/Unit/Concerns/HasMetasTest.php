<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\Unit\Concerns;

use Dbout\WpOrm\Concerns\HasMetas;
use Dbout\WpOrm\Models\Meta\AbstractMeta;
use Dbout\WpOrm\Models\Meta\PostMeta;
use Dbout\WpOrm\Models\Meta\UserMeta;
use Dbout\WpOrm\Models\Post;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

#[CoversTrait(HasMetas::class)]
class HasMetasTest extends TestCase
{
    /**
     * Pin: meta classes do not use SoftDeletes, so HasMetas::deleteMeta()
     * calling forceDelete() is currently equivalent to delete().
     *
     * The use of forceDelete() in deleteMeta() is misleading because none of
     * the meta classes use SoftDeletes. The call happens to be harmless TODAY,
     * but if SoftDeletes is ever added to AbstractMeta, forceDelete() would
     * silently bypass the soft-delete mechanism. This pin forces a
     * re-evaluation of deleteMeta() in that case (it should switch to delete()).
     *
     * @param class-string<AbstractMeta> $metaClass
     * @return void
     */
    #[Group('regression-pin')]
    #[TestWith([PostMeta::class])]
    #[TestWith([UserMeta::class])]
    public function testMetaClassDoesNotUseSoftDeletes(string $metaClass): void
    {
        $traits = class_uses_recursive($metaClass);

        $this->assertNotContains(
            SoftDeletes::class,
            $traits,
            sprintf(
                'Pin: %s does not use SoftDeletes today, so HasMetas::deleteMeta() '
                . 'using forceDelete() is harmless. If you add SoftDeletes here, '
                . 'switch deleteMeta() to delete().',
                $metaClass
            )
        );
    }

    /**
     * @return void
     */
    public function testMetaHasCastWithProperty(): void
    {
        $model = new class () extends Post {
            protected array $metaCasts  = [
                'my_meta' => 'int',
            ];
        };

        $this->assertTrue($model->metaHasCast('my_meta'));
        $this->assertTrue($model->metaHasCast('my_meta', 'int'));
        $this->assertFalse($model->metaHasCast('my_meta', 'boolean'));
        $this->assertFalse($model->metaHasCast('custom-meta'));
    }

    /**
     * @return void
     */
    public function testMetaHasCastWithMetaCastsFunction(): void
    {
        $model = new class () extends Post {
            /**
             * @inheritDoc
             */
            protected function metaCasts(): array
            {
                return [
                    'my_meta' => 'int',
                ];
            }
        };

        $this->assertTrue($model->metaHasCast('my_meta'));
        $this->assertTrue($model->metaHasCast('my_meta', 'int'));
        $this->assertFalse($model->metaHasCast('my_meta', 'boolean'));
        $this->assertFalse($model->metaHasCast('custom-meta'));
    }

    /**
     * @return void
     */
    public function testGetMetaCasts(): void
    {
        $model = new class () extends Post {
            protected array $metaCasts  = [
                'custom-meta' => 'int',
            ];

            /**
             * @inheritDoc
             */
            protected function metaCasts(): array
            {
                return [
                    'my-meta' => 'string',
                ];
            }
        };

        $this->assertEquals([
            'custom-meta' => 'int',
            'my-meta' => 'string',
        ], $model->getMetaCasts());
    }

    /**
     * @param string $castType
     * @param mixed $value
     * @param mixed $expected
     * @return void
     */
    #[DataProvider('castMetaProvider')]
    public function testCastMeta(string $castType, mixed $value, mixed $expected): void
    {
        $model = new class () extends Post {
            protected array $metaCasts = [];

            public function setCastType(string $type): void
            {
                $this->metaCasts = ['test_key' => $type];
            }

            public function callCastMeta(string $key, mixed $value): mixed
            {
                return $this->castMeta($key, $value);
            }
        };

        $model->setCastType($castType);
        $this->assertSame($expected, $model->callCastMeta('test_key', $value));
    }

    /**
     * @return iterable<string, array{string, mixed, mixed}>
     */
    public static function castMetaProvider(): iterable
    {
        yield 'int cast' => ['int', '42', 42];
        yield 'integer cast' => ['integer', '10', 10];
        yield 'float cast' => ['float', '3.14', 3.14];
        yield 'double cast' => ['double', '2.71', 2.71];
        yield 'string cast' => ['string', 123, '123'];
        yield 'bool true cast' => ['bool', '1', true];
        yield 'bool false cast' => ['bool', '0', false];
        yield 'boolean cast' => ['boolean', '1', true];
        yield 'decimal cast (2 digits)' => ['decimal:2', '3.14159', '3.14'];
        yield 'decimal cast (4 digits)' => ['decimal:4', '3.1', '3.1000'];
        yield 'decimal cast (0 digits)' => ['decimal:0', '3.7', '4'];
    }

    /**
     * @param string $castType
     * @return void
     */
    #[DataProvider('castMetaNullProvider')]
    public function testCastMetaReturnsNullForNullValue(string $castType): void
    {
        $model = new class () extends Post {
            protected array $metaCasts = [];

            public function setCastType(string $type): void
            {
                $this->metaCasts = ['test_key' => $type];
            }

            public function callCastMeta(string $key, mixed $value): mixed
            {
                return $this->castMeta($key, $value);
            }
        };

        $model->setCastType($castType);
        $this->assertNull($model->callCastMeta('test_key', null));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function castMetaNullProvider(): iterable
    {
        yield 'int' => ['int'];
        yield 'integer' => ['integer'];
        yield 'float' => ['float'];
        yield 'double' => ['double'];
        yield 'real' => ['real'];
        yield 'string' => ['string'];
        yield 'bool' => ['bool'];
        yield 'boolean' => ['boolean'];
        yield 'array' => ['array'];
        yield 'json' => ['json'];
        yield 'object' => ['object'];
        yield 'collection' => ['collection'];
        yield 'date' => ['date'];
        yield 'datetime' => ['datetime'];
        yield 'immutable_date' => ['immutable_date'];
        yield 'immutable_datetime' => ['immutable_datetime'];
        yield 'timestamp' => ['timestamp'];
        yield 'decimal' => ['decimal:2'];
        yield 'custom_datetime' => ['datetime:Y-m-d'];
        yield 'immutable_custom_datetime' => ['immutable_datetime:Y-m-d'];
    }
}
