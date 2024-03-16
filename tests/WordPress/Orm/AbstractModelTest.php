<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Orm;

use Dbout\WpOrm\Models\Article;
use Dbout\WpOrm\Tests\WordPress\TestCase;
use Illuminate\Database\QueryException;

/**
 * @coversDefaultClass \Dbout\WpOrm\Orm\AbstractModel
 */
class AbstractModelTest extends TestCase
{
    /**
     * @param string $saveMethod
     * @return void
     * @covers ::save
     * @covers ::saveOrFail
     * @dataProvider providerTestSave
     */
    public function testSuccessSave(string $saveMethod): void
    {
        $model = new Article();
        $model->$saveMethod();

        $expectedId = $model->getId();
        $this->assertIsNumeric($expectedId);

        $expectedModel = get_post($expectedId);
        $this->assertInstanceOf(\WP_Post::class, $expectedModel);
        $this->assertEquals($expectedId, $expectedModel->ID);
    }

    /**
     * @param string $saveMethod
     * @return void
     * @covers ::save
     * @covers ::saveOrFail
     * @dataProvider providerTestSave
     */
    public function testSaveWithInvalidProperty(string $saveMethod): void
    {
        $model = new Article([
            'custom_column' => '15',
        ]);

        // Maybe more strict ?
        // Check this message: Unknown column 'custom_column' in 'field list'
        $this->expectException(QueryException::class);
        $model->$saveMethod();
    }

    /**
     * @return \Generator
     */
    protected function providerTestSave(): \Generator
    {
        yield 'With save function' => [
            'save',
        ];

        yield 'With saveOrFail function' => [
            'saveOrFail',
        ];
    }
}
