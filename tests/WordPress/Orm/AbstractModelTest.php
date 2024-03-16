<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Orm;

use Dbout\WpOrm\Models\Article;
use Dbout\WpOrm\Models\Post;
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
     * @dataProvider providerTestSaveNewObject
     */
    public function testSuccessNSaveNewObject(string $saveMethod): void
    {
        $model = new Article();
        $model->setPostName('hello-world');
        $model->setPostContent('My hello world content');
        $model->setPostTitle('Hello world');
        $model->$saveMethod();

        $expectedId = $model->getId();
        $this->assertIsNumeric($expectedId);

        $this->assertPostEqualToWpObject($model, get_post($expectedId));
    }

    /**
     * @param string $saveMethod
     * @return void
     * @covers ::save
     * @covers ::saveOrFail
     * @dataProvider providerTestSaveNewObject
     */
    public function testSaveWithInvalidProperty(string $saveMethod): void
    {
        $fakeColumn = 'custom_column';
        $model = new Article([
            $fakeColumn => '15',
        ]);

        $this->expectException(QueryException::class);
        $this->expectExceptionUnknownColumn($fakeColumn);
        $model->$saveMethod();
    }

    /**
     * @return \Generator
     */
    protected function providerTestSaveNewObject(): \Generator
    {
        yield 'With save function' => [
            'save',
        ];

        yield 'With saveOrFail function' => [
            'saveOrFail',
        ];
    }

    /**
     * @return void
     * @covers ::delete
     */
    public function testDelete(): void
    {
        $postId = self::factory()->post->create();
        $post = Post::find($postId);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertTrue($post->delete());

        $post = Post::find($postId);
        $this->assertNull($post, 'The post was not deleted correctly.');
    }
}
