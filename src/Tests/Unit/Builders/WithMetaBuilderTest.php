<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\Unit\Builders;

use Dbout\WpOrm\Builders\PostBuilder;
use Dbout\WpOrm\Exceptions\WpOrmException;
use Dbout\WpOrm\Models\Post;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @since 3.0.0
 * @coversDefaultClass \Dbout\WpOrm\Builders\AbstractWithMetaBuilder
 */
class WithMetaBuilderTest extends TestCase
{
    private PostBuilder $builder;

    private Post&MockObject $post;

    /**
     * @inheritDoc
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $queryBuilder = new \Illuminate\Database\Query\Builder(
            $this->createMock(\Illuminate\Database\MySqlConnection::class),
            new \Illuminate\Database\Query\Grammars\Grammar(),
            new \Illuminate\Database\Query\Processors\Processor()
        );

        $post = $this->createPartialMock(Post::class, ['getTable']);
        $post->method('getTable')->willReturn('posts');
        $this->post = $post;
        $builder = new PostBuilder($queryBuilder);
        $builder->setModel($this->post);
        $this->builder = $builder;
    }

    /**
     * @param string $metaKey
     * @param string|null $alias
     * @param string $expectedQuery
     * @throws WpOrmException
     * @return void
     * @covers ::addMetaToSelect
     * @dataProvider providerTestAddMetaToSelect
     */
    public function testAddMetaToSelect(string $metaKey, ?string $alias, string $expectedQuery): void
    {
        $this->builder->addMetaToSelect($metaKey, $alias);
        $query = $this->builder->toSql();
        $this->assertEquals($expectedQuery, $query);
    }

    /**
     * @return array
     */
    public static function providerTestAddMetaToSelect(): array
    {
        return [
            'Without alias' => [
                'my_meta',
                null,
                'select "posts".*, "my_meta"."meta_value" as "my_meta_value" from "posts" inner join "postmeta" as "my_meta" on "my_meta"."meta_key" = "my_meta" and "my_meta"."post_id" = "posts"."ID"',
            ],
            'With alias' => [
                'first_name',
                'my_custom_alias',
                'select "posts".*, "first_name"."meta_value" as "my_custom_alias" from "posts" inner join "postmeta" as "first_name" on "first_name"."meta_key" = "first_name" and "first_name"."post_id" = "posts"."ID"',
            ],
        ];
    }

    /**
     * @param array $metas
     * @param string $expectedQuery
     * @throws WpOrmException
     * @return void
     * @covers ::addMetasToSelect
     * @dataProvider providerTestAddMetasToSelect
     */
    public function testAddMetasToSelect(array $metas, string $expectedQuery): void
    {
        $this->builder->addMetasToSelect($metas);
        $query = $this->builder->toSql();
        $this->assertEquals($expectedQuery, $query);
    }

    /**
     * @return array
     */
    public static function providerTestAddMetasToSelect(): array
    {
        return [
            'Without alias' => [
                [
                    'firstname',
                    'lastname',
                ],
                'select "posts".*, "firstname"."meta_value" as "firstname_value", "lastname"."meta_value" as "lastname_value" from "posts" inner join "postmeta" as "firstname" on "firstname"."meta_key" = "firstname" and "firstname"."post_id" = "posts"."ID" inner join "postmeta" as "lastname" on "lastname"."meta_key" = "lastname" and "lastname"."post_id" = "posts"."ID"',
            ],
            'With alias' => [
                [
                    'my_meta' => 'firstname',
                    'second_meta' => 'lastname',
                ],
                'select "posts".*, "firstname"."meta_value" as "my_meta", "lastname"."meta_value" as "second_meta" from "posts" inner join "postmeta" as "firstname" on "firstname"."meta_key" = "firstname" and "firstname"."post_id" = "posts"."ID" inner join "postmeta" as "lastname" on "lastname"."meta_key" = "lastname" and "lastname"."post_id" = "posts"."ID"',
            ],
            'On meta with alias on another one without alias' => [
                [
                    'my_meta' => 'street_1',
                    'lastname',
                ],
                'select "posts".*, "street_1"."meta_value" as "my_meta", "lastname"."meta_value" as "lastname_value" from "posts" inner join "postmeta" as "street_1" on "street_1"."meta_key" = "street_1" and "street_1"."post_id" = "posts"."ID" inner join "postmeta" as "lastname" on "lastname"."meta_key" = "lastname" and "lastname"."post_id" = "posts"."ID"',
            ],
        ];
    }

    /**
     * @throws WpOrmException
     * @return void
     * @covers ::joinToMeta
     */
    public function testJoinToMeta(): void
    {
        $this->post->expects($this->once())->method('getTable');
        $this->builder->joinToMeta('my_meta');
        $query = $this->builder->toSql();
        $this->assertEquals(
            'select "posts".* from "posts" inner join "postmeta" as "my_meta" on "my_meta"."meta_key" = "my_meta" and "my_meta"."post_id" = "posts"."ID"',
            $query
        );
    }

    /**
     * @throws WpOrmException
     * @return void
     * @covers ::addMetaToFilter
     */
    public function testAddMetaToFilter(): void
    {
        $this->builder->addMetaToFilter('firstname', 'Dimitri');
        $query = $this->builder->toSql();

        $this->assertEquals(
            'select "posts".* from "posts" inner join "postmeta" as "firstname" on "firstname"."meta_key" = "firstname" and "firstname"."post_id" = "posts"."ID" where "firstname"."meta_value" = ?',
            $query,
        );

        $this->assertEquals(
            [
                'Dimitri',
            ],
            $this->builder->getBindings(),
        );
    }
}
