<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Orm;

use Dbout\WpOrm\Models\Article;
use Dbout\WpOrm\Models\Option;
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

        $this->assertTrue($model->$saveMethod());
        $expectedId = $model->getId();
        $this->assertIsNumeric($expectedId);

        $this->assertPostEqualToWpObject($model, get_post($expectedId));
        $this->assertEqualLastInsertId($expectedId);
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

    /**
     * Test if all attributes have been overridden.
     *
     * @return void
     * @covers ::fill
     * @covers ::guard
     */
    public function testFillWithEmptyGuarded(): void
    {
        $post = new Post();
        $post->setPostType('article');
        $post->setPostName('the-article');
        $post->setPostContent('the article content');
        $post->guard([]);
        $post->fill([
            'post_type' => 'product',
            'post_name' => 'my-filled-post',
            'post_content' => 'The post content',
        ]);

        $this->assertEquals('product', $post->getPostType());
        $this->assertEquals('my-filled-post', $post->getPostName());
        $this->assertEquals('The post content', $post->getPostContent());
    }

    /**
     * @return void
     * @covers ::fill
     * @covers ::guard
     */
    public function testFillWithGuardedAttributes(): void
    {
        $post = new Post();
        $post->setPostType('article');
        $post->setPostName('the-article');
        $post->setPostContent('the article content.');
        $post->guard(['post_type']);
        $post->fill([
            'post_type' => 'product',
            'post_name' => 'my-filled-post',
            'post_content' => 'The post content',
            'test' => 'custom test column',
        ]);

        $this->assertEquals('article', $post->getPostType(), 'This attribute should not be changed because it is protected.');
        $this->assertEquals('my-filled-post', $post->getPostName());
        $this->assertEquals('The post content', $post->getPostContent());
        $this->assertNull($post->getAttribute('test'), 'This attribute must be empty because it does not exist in the posts table.');
    }

    /**
     * @return void
     * @covers ::upsert
     */
    public function testUpsertWithOneNewObject(): void
    {
        $result = Option::upsert(
            [
                'option_name' => '_upsert_new_data',
                'option_value' => 'John D.',
            ],
            ['option_name']
        );

        $this->assertEquals(1, $result, 'One row must be saved in a database');
        $this->checkUpsertOption('_upsert_new_data', 'John D.');
    }

    /**
     * @return void
     * @covers ::upsert
     */
    public function testUpsertWithMultipleNewObjects(): void
    {
        $result = Option::upsert(
            [
                'option_name' => '_upsert_architect_1',
                'option_value' => 'Zaha H.',
            ],
            [
                'option_name' => '_upsert_architect_2',
                'option_value' => 'Norman F.',
            ],
            ['option_name']
        );

        $this->assertEquals(2, $result, 'Twos rows must be saved in a database');
        $this->checkUpsertOption('_upsert_architect_1', 'Zaha H.');
        $this->checkUpsertOption('_upsert_architect_2', 'Norman F.');
    }

    public function _testUpsertWithExistingObject(): void
    {

    }

    /**
     * @param string $optionName
     * @param string $expectedValue
     * @return void
     */
    private function checkUpsertOption(string $optionName, string $expectedValue): void
    {
        $option = Option::findOneByName($optionName);
        $this->assertInstanceOf(Option::class, $option);
        $this->assertEquals($expectedValue, $option->getOptionValue());

        $wpOpt = get_option($optionName);
        $this->assertEquals($expectedValue, $wpOpt);
    }
}
