<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Builders;

use Dbout\WpOrm\Builders\PostBuilder;
use Dbout\WpOrm\Exceptions\WpOrmException;
use Dbout\WpOrm\Models\Post;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class PostBuilderTest extends TestCase
{
    /**
     * @covers PostBuilder::joinToMeta
     * @covers PostBuilder::addMetaToSelect
     * @throws WpOrmException
     * @return void
     */
    public function testAddMetaToSelect(): void
    {
        $post = new Post();
        $post->setPostTitle('Meta select test');
        $post->setPostName('meta-select-test');
        $post->setPostType('post');
        $this->assertTrue($post->save());
        $post->setMeta('color', 'blue');

        /** @var Post $result */
        $result = Post::query()
            ->addMetaToSelect('color')
            ->where(Post::POST_ID, $post->getId())
            ->first();

        $this->assertNotNull($result);
        $this->assertEquals('blue', $result->getAttribute('color_value'));
    }

    /**
     * @covers PostBuilder::addMetaToSelect
     * @throws WpOrmException
     * @return void
     */
    public function testAddMetaToSelectWithAlias(): void
    {
        $post = new Post();
        $post->setPostTitle('Meta alias test');
        $post->setPostName('meta-alias-test');
        $post->setPostType('post');
        $this->assertTrue($post->save());
        $post->setMeta('color', 'red');

        /** @var Post $result */
        $result = Post::query()
            ->addMetaToSelect('color', 'my_color')
            ->where(Post::POST_ID, $post->getId())
            ->first();

        $this->assertNotNull($result);
        $this->assertEquals('red', $result->getAttribute('my_color'));
    }

    /**
     * @covers PostBuilder::addMetasToSelect
     * @throws WpOrmException
     * @return void
     */
    public function testAddMetasToSelect(): void
    {
        $post = new Post();
        $post->setPostTitle('Multi meta test');
        $post->setPostName('multi-meta-test');
        $post->setPostType('post');
        $this->assertTrue($post->save());
        $post->setMeta('color', 'green');
        $post->setMeta('size', 'large');

        /** @var Post $result */
        $result = Post::query()
            ->addMetasToSelect(['color', 'size'])
            ->where(Post::POST_ID, $post->getId())
            ->first();

        $this->assertNotNull($result);
        $this->assertEquals('green', $result->getAttribute('color_value'));
        $this->assertEquals('large', $result->getAttribute('size_value'));
    }

    /**
     * @covers PostBuilder::addMetasToSelect
     * @throws WpOrmException
     * @return void
     */
    public function testAddMetasToSelectWithAliases(): void
    {
        $post = new Post();
        $post->setPostTitle('Multi meta alias test');
        $post->setPostName('multi-meta-alias-test');
        $post->setPostType('post');
        $this->assertTrue($post->save());
        $post->setMeta('color', 'yellow');
        $post->setMeta('size', 'small');

        /** @var Post $result */
        $result = Post::query()
            ->addMetasToSelect(['my_color' => 'color', 'my_size' => 'size'])
            ->where(Post::POST_ID, $post->getId())
            ->first();

        $this->assertNotNull($result);
        $this->assertEquals('yellow', $result->getAttribute('my_color'));
        $this->assertEquals('small', $result->getAttribute('my_size'));
    }

    /**
     * @covers PostBuilder::joinToMeta
     * @covers PostBuilder::addMetaToFilter
     * @throws WpOrmException
     * @return void
     */
    public function testAddMetaToFilter(): void
    {
        $post1 = new Post();
        $post1->setPostTitle('Filter test 1');
        $post1->setPostName('filter-test-1');
        $post1->setPostType('post');
        $this->assertTrue($post1->save());
        $post1->setMeta('priority', 'high');

        $post2 = new Post();
        $post2->setPostTitle('Filter test 2');
        $post2->setPostName('filter-test-2');
        $post2->setPostType('post');
        $this->assertTrue($post2->save());
        $post2->setMeta('priority', 'low');

        $results = Post::query()
            ->addMetaToFilter('priority', 'high')
            ->get();

        $ids = $results->pluck(Post::POST_ID)->toArray();
        $this->assertContains($post1->getId(), $ids);
        $this->assertNotContains($post2->getId(), $ids);
    }

    /**
     * @covers PostBuilder::addMetaToFilter
     * @throws WpOrmException
     * @return void
     */
    public function testAddMetaToFilterWithOperator(): void
    {
        $post1 = new Post();
        $post1->setPostTitle('Operator test 1');
        $post1->setPostName('operator-test-1');
        $post1->setPostType('post');
        $this->assertTrue($post1->save());
        $post1->setMeta('level', 'B');

        $post2 = new Post();
        $post2->setPostTitle('Operator test 2');
        $post2->setPostName('operator-test-2');
        $post2->setPostType('post');
        $this->assertTrue($post2->save());
        $post2->setMeta('level', 'A');

        $results = Post::query()
            ->addMetaToFilter('level', 'A', '>')
            ->get();

        $ids = $results->pluck(Post::POST_ID)->toArray();
        $this->assertContains($post1->getId(), $ids);
        $this->assertNotContains($post2->getId(), $ids);
    }

    /**
     * @covers PostBuilder::joinToMeta
     * @throws WpOrmException
     * @return void
     */
    public function testJoinToMetaDoesNotDuplicate(): void
    {
        $post = new Post();
        $post->setPostTitle('Duplicate join test');
        $post->setPostName('duplicate-join-test');
        $post->setPostType('post');
        $this->assertTrue($post->save());
        $post->setMeta('color', 'blue');

        $results = Post::query()
            ->addMetaToSelect('color')
            ->addMetaToFilter('color', 'blue')
            ->get();

        $this->assertNotNull($results->first());
        $this->assertEquals('blue', $results->first()->getAttribute('color_value'));
    }

    /**
     * @covers PostBuilder::addMetaToSelect
     * @covers PostBuilder::addMetaToFilter
     * @throws WpOrmException
     * @return void
     */
    public function testCombineMetaSelectAndFilter(): void
    {
        $post = new Post();
        $post->setPostTitle('Combine test');
        $post->setPostName('combine-test');
        $post->setPostType('post');
        $this->assertTrue($post->save());
        $post->setMeta('color', 'blue');
        $post->setMeta('size', 'large');

        $results = Post::query()
            ->addMetaToSelect('size')
            ->addMetaToFilter('color', 'blue')
            ->where(Post::POST_ID, $post->getId())
            ->get();

        $first = $results->first();
        $this->assertNotNull($first);
        $this->assertEquals('large', $first->getAttribute('size_value'));
    }

    /**
     * @covers PostBuilder::joinToMeta
     * @throws WpOrmException
     * @return void
     */
    public function testJoinToMetaWithLeftJoin(): void
    {
        $postWith = new Post();
        $postWith->setPostTitle('With meta');
        $postWith->setPostName('with-meta');
        $postWith->setPostType('post');
        $this->assertTrue($postWith->save());
        $postWith->setMeta('badge', 'gold');

        $postWithout = new Post();
        $postWithout->setPostTitle('Without meta');
        $postWithout->setPostName('without-meta');
        $postWithout->setPostType('post');
        $this->assertTrue($postWithout->save());

        /** @var array $results */
        $results = Post::query()
            ->joinToMeta('badge', 'left')
            ->whereIn(Post::POST_ID, [$postWith->getId(), $postWithout->getId()])
            ->get();

        $this->assertCount(2, $results);
    }

    /**
     * @covers PostBuilder::joinToMeta
     * @return void
     */
    public function testJoinToMetaRejectsInvalidIdentifier(): void
    {
        $this->expectException(WpOrmException::class);
        $this->expectExceptionMessageMatches('/Invalid meta key/');

        Post::query()->joinToMeta("color'; DROP TABLE wp_posts; --");
    }

    /**
     * @covers PostBuilder::addMetaToSelect
     * @return void
     */
    public function testAddMetaToSelectRejectsInvalidAlias(): void
    {
        $this->expectException(WpOrmException::class);
        $this->expectExceptionMessageMatches('/Invalid meta select alias/');

        Post::query()->addMetaToSelect('color', 'bad alias');
    }

    /**
     * @covers PostBuilder::addMetaToFilter
     * @return void
     */
    public function testAddMetaToFilterRejectsInvalidIdentifier(): void
    {
        $this->expectException(WpOrmException::class);
        $this->expectExceptionMessageMatches('/Invalid meta key/');

        Post::query()->addMetaToFilter('1invalid', 'value');
    }

    /**
     * @covers PostBuilder::joinToMeta
     * @return void
     */
    public function testJoinToMetaUsesBoundMetaKeyValue(): void
    {
        $post = new Post();
        $post->setPostTitle('Bound binding test');
        $post->setPostName('bound-binding-test');
        $post->setPostType('post');
        $this->assertTrue($post->save());
        $post->setMeta('color', "red'; DROP TABLE wp_postmeta; --");

        $results = Post::query()
            ->addMetaToSelect('color')
            ->where(Post::POST_ID, $post->getId())
            ->get();

        $first = $results->first();
        $this->assertNotNull($first);
        $this->assertEquals(
            "red'; DROP TABLE wp_postmeta; --",
            $first->getAttribute('color_value')
        );
    }
}
