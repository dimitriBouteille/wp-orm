<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Builders;

use Dbout\WpOrm\Builders\PostBuilder;
use Dbout\WpOrm\Exceptions\WpOrmException;
use Dbout\WpOrm\Models\Post;
use Dbout\WpOrm\Tests\WordPress\Support\BuildsTestPost;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class PostBuilderTest extends TestCase
{
    use BuildsTestPost;

    /**
     * @param string|null $alias
     * @param string $expectedAttribute
     * @covers PostBuilder::joinToMeta
     * @covers PostBuilder::addMetaToSelect
     * @dataProvider providerAddMetaToSelect
     * @throws WpOrmException
     * @return void
     */
    public function testAddMetaToSelect(?string $alias, string $expectedAttribute): void
    {
        $post = $this->aPostWithMetas(['color' => 'blue'], 'Add meta to select');

        /** @var Post $result */
        $result = Post::query()
            ->addMetaToSelect('color', $alias)
            ->where(Post::POST_ID, $post->getId())
            ->first();

        $this->assertNotNull($result);
        $this->assertEquals('blue', $result->getAttribute($expectedAttribute));
    }

    /**
     * @return \Generator<string, array{?string, string}>
     */
    public static function providerAddMetaToSelect(): \Generator
    {
        yield 'default alias' => [null, 'color_value'];
        yield 'custom alias'  => ['my_color', 'my_color'];
    }

    /**
     * @param array<int|string, string> $argument
     * @param array<string, string> $aliasFor
     * @covers PostBuilder::addMetasToSelect
     * @dataProvider providerAddMetasToSelect
     * @throws WpOrmException
     * @return void
     */
    public function testAddMetasToSelect(array $argument, array $aliasFor): void
    {
        $post = $this->aPostWithMetas([
            'color' => 'green',
            'size' => 'large',
        ], 'Add metas to select');

        /** @var Post $result */
        $result = Post::query()
            ->addMetasToSelect($argument)
            ->where(Post::POST_ID, $post->getId())
            ->first();

        $this->assertNotNull($result);
        $this->assertEquals('green', $result->getAttribute($aliasFor['color']));
        $this->assertEquals('large', $result->getAttribute($aliasFor['size']));
    }

    /**
     * @return \Generator<string, array{array<int|string, string>, array<string, string>}>
     */
    public static function providerAddMetasToSelect(): \Generator
    {
        yield 'list (default aliases)' => [
            ['color', 'size'],
            ['color' => 'color_value', 'size' => 'size_value'],
        ];
        yield 'map (custom aliases)' => [
            ['my_color' => 'color', 'my_size' => 'size'],
            ['color' => 'my_color', 'size' => 'my_size'],
        ];
    }

    /**
     * @covers PostBuilder::joinToMeta
     * @covers PostBuilder::addMetaToFilter
     * @throws WpOrmException
     * @return void
     */
    public function testAddMetaToFilter(): void
    {
        $highPost = $this->aPostWithMetas(['priority' => 'high'], 'Filter test 1');
        $lowPost = $this->aPostWithMetas(['priority' => 'low'], 'Filter test 2');

        $results = Post::query()
            ->addMetaToFilter('priority', 'high')
            ->get();

        $ids = $results->pluck(Post::POST_ID)->toArray();
        $this->assertContains($highPost->getId(), $ids);
        $this->assertNotContains($lowPost->getId(), $ids);
    }

    /**
     * @covers PostBuilder::addMetaToFilter
     * @throws WpOrmException
     * @return void
     */
    public function testAddMetaToFilterWithOperator(): void
    {
        $bPost = $this->aPostWithMetas(['level' => 'B'], 'Operator test 1');
        $aPost = $this->aPostWithMetas(['level' => 'A'], 'Operator test 2');

        $results = Post::query()
            ->addMetaToFilter('level', 'A', '>')
            ->get();

        $ids = $results->pluck(Post::POST_ID)->toArray();
        $this->assertContains($bPost->getId(), $ids);
        $this->assertNotContains($aPost->getId(), $ids);
    }

    /**
     * @covers PostBuilder::joinToMeta
     * @throws WpOrmException
     * @return void
     */
    public function testJoinToMetaDoesNotDuplicate(): void
    {
        $post = $this->aPostWithMetas(['color' => 'blue'], 'Duplicate join test');

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
        $post = $this->aPostWithMetas([
            'color' => 'blue',
            'size' => 'large',
        ], 'Combine test');

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
        $postWith = $this->aPostWithMetas(['badge' => 'gold'], 'With meta');
        $postWithout = $this->aPost('Without meta');

        /** @var array $results */
        $results = Post::query()
            ->joinToMeta('badge', 'left')
            ->whereIn(Post::POST_ID, [$postWith->getId(), $postWithout->getId()])
            ->get();

        $this->assertCount(2, $results);
    }

    /**
     * @return void
     * @covers PostBuilder::joinToMeta
     */
    public function testJoinToMetaRejectsInvalidIdentifier(): void
    {
        $this->expectException(WpOrmException::class);
        $this->expectExceptionMessageMatches('/Invalid meta key/');

        Post::query()->joinToMeta("color'; DROP TABLE wp_posts; --");
    }

    /**
     * @return void
     * @covers PostBuilder::addMetaToSelect
     */
    public function testAddMetaToSelectRejectsInvalidAlias(): void
    {
        $this->expectException(WpOrmException::class);
        $this->expectExceptionMessageMatches('/Invalid meta select alias/');

        Post::query()->addMetaToSelect('color', 'bad alias');
    }

    /**
     * @return void
     * @covers PostBuilder::addMetaToFilter
     */
    public function testAddMetaToFilterRejectsInvalidIdentifier(): void
    {
        $this->expectException(WpOrmException::class);
        $this->expectExceptionMessageMatches('/Invalid meta key/');

        Post::query()->addMetaToFilter('1invalid', 'value');
    }

    /**
     * @return void
     * @covers PostBuilder::joinToMeta
     */
    public function testJoinToMetaUsesBoundMetaKeyValue(): void
    {
        $post = $this->aPostWithMetas([
            'color' => "red'; DROP TABLE wp_postmeta; --",
        ], 'Bound binding test');

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
