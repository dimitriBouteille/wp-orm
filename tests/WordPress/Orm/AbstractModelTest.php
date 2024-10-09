<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
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
    public function testSuccessSaveNewObject(string $saveMethod): void
    {
        $model = new Article();
        $model->setPostName('hello-world');
        $model->setPostContent('My hello world content');
        $model->setPostTitle('Hello world');

        $this->assertTrue($model->$saveMethod());
        $expectedId = $model->getId();
        $this->assertIsNumeric($expectedId);

        $this->assertPostEqualsToWpPost($model);
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
    public function testUpsertWithOneNewObjects(): void
    {
        Option::upsert(
            [
                [
                    'option_name' => '_upsert_architect_0',
                    'option_value' => 'John D.',
                ],
                [
                    'option_name' => '_upsert_architect_1',
                    'option_value' => 'Zaha H.',
                ],
            ],
            ['option_name']
        );

        $this->checkUpsertOption('_upsert_architect_0', 'John D.');
        $this->checkUpsertOption('_upsert_architect_1', 'Zaha H.');
    }

    /**
     * @return void
     * @covers ::upsert
     */
    public function testUpsertWithExistingObjects(): void
    {
        add_option('store_phone', '15 15 15');
        add_option('store_email', 'boutique@test.fr');
        add_option('store_address', 'Road test');

        Option::upsert(
            [
                [
                    'option_name' => 'store_phone',
                    'option_value' => '15 15 15',
                ],
                [
                    'option_name' => 'store_email',
                    'option_value' => 'boutique@test.fr',
                ],
                [
                    'option_name' => 'store_address',
                    'option_value' => 'Road of paris',
                ],
            ],
            ['option_name']
        );

        $this->checkUpsertOption('store_phone', '15 15 15');
        $this->checkUpsertOption('store_email', 'boutique@test.fr');

        // Check if value is updated
        $this->checkUpsertOption('store_address', 'Road of paris');
    }

    /**
     * @return void
     * @covers ::upsert
     */
    public function testUpsertWithUpdateKey(): void
    {
        add_option('store_latitude', '75.652');

        Option::upsert(
            [
                [
                    'option_name' => 'store_latitude',
                    'option_value' => 40.111,
                    'autoload' => 'no',
                ],
            ],
            ['option_name'],
            ['autoload']
        );

        // Check if value is not update updated
        $option = $this->checkUpsertOption('store_latitude', 75.652);
        $this->assertEquals('no', $option?->getAutoload());
    }

    /**
     * @param string $optionName
     * @param mixed $expectedValue
     * @return Option|null
     */
    private function checkUpsertOption(string $optionName, mixed $expectedValue): ?Option
    {
        $option = Option::findOneByName($optionName);
        $this->assertInstanceOf(Option::class, $option);
        $this->assertEquals($expectedValue, $option->getOptionValue());

        \wp_cache_delete('alloptions', 'options');
        $wpOpt = \get_option($optionName);
        $this->assertEquals($expectedValue, $wpOpt);

        return $option;
    }
}
