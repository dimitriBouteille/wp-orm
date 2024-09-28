<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\Unit\Concerns;

use Dbout\WpOrm\Models\Post;
use PHPUnit\Framework\TestCase;

class HasMetasTest extends TestCase
{
    /**
     * @return void
     * @covers \Dbout\WpOrm\Concerns\HasMetas::metaHasCast
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
     * @covers \Dbout\WpOrm\Concerns\HasMetas::metaHasCast
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
     * @covers \Dbout\WpOrm\Concerns\HasMetas::getMetaCasts
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
}
