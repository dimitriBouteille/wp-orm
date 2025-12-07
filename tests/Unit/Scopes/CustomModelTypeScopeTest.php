<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\Unit\Scopes;

use Dbout\WpOrm\Api\CustomModelTypeInterface;
use Dbout\WpOrm\Builders\CommentBuilder;
use Dbout\WpOrm\Builders\PostBuilder;
use Dbout\WpOrm\Exceptions\WpOrmException;
use Dbout\WpOrm\Models\Attachment;
use Dbout\WpOrm\Models\CustomComment;
use Dbout\WpOrm\Models\Option;
use Dbout\WpOrm\Models\Post;
use Dbout\WpOrm\Scopes\CustomModelTypeScope;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(CustomModelTypeScope::class)]
#[CoversMethod(CustomModelTypeScope::class, 'apply')]
class CustomModelTypeScopeTest extends TestCase
{
    private CustomModelTypeScope $scope;

    protected function setUp(): void
    {
        parent::setUp();
        $this->scope = new CustomModelTypeScope();
    }

    /**
     * @return void
     */
    public function testThrowsExceptionWhenModelDoesNotImplementInterface(): void
    {
        $model = new Option();
        $builder = $this->createStub(Builder::class);

        $this->expectException(WpOrmException::class);
        $this->expectExceptionMessage(sprintf(
            'The object %s must be implement %s.',
            Option::class,
            CustomModelTypeInterface::class
        ));

        $this->scope->apply($builder, $model);
    }

    /**
     * @throws WpOrmException
     * @return void
     */
    public function testAppliesWhereClauseForAttachmentModel(): void
    {
        $model = new Attachment();
        $connection = $this->createStub(\Illuminate\Database\MySqlConnection::class);
        $query = new \Illuminate\Database\Query\Builder(
            $connection,
            new Grammar($this->createStub(Connection::class)),
            new Processor()
        );
        $builder = new PostBuilder($query);

        $this->scope->apply($builder, $model);

        $this->assertEquals('select * where "post_type" = ?', $builder->toSql());
        $this->assertEquals(['attachment'], $builder->getBindings());
    }

    /**
     * @throws WpOrmException
     * @return void
     */
    public function testCallsWhereMethodWithCorrectParameters(): void
    {
        $model = new class () extends Post implements CustomModelTypeInterface {
            public function getCustomTypeColumn(): string
            {
                return 'test_column';
            }

            public function getCustomTypeCode(): string
            {
                return 'test_value';
            }
        };

        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())
            ->method('where')
            ->with('test_column', 'test_value');

        $this->scope->apply($builder, $model);
    }

    /**
     * @throws WpOrmException
     * @return void
     */
    public function testAppliesWhereClauseForCustomCommentModel(): void
    {
        $model = new class () extends CustomComment {
            protected string $_type = 'review';
        };

        $connection = $this->createStub(\Illuminate\Database\MySqlConnection::class);
        $query = new \Illuminate\Database\Query\Builder(
            $connection,
            new Grammar($this->createStub(Connection::class)),
            new Processor()
        );
        $builder = new CommentBuilder($query);

        $this->scope->apply($builder, $model);

        $this->assertEquals('select * where "comment_type" = ?', $builder->toSql());
        $this->assertEquals(['review'], $builder->getBindings());
    }

    /**
     * Test that the scope works with different custom type values.
     *
     * @throws WpOrmException
     * @return void
     */
    public function testWorksWithDifferentCustomTypeValues(): void
    {
        $model = new class () extends Post implements CustomModelTypeInterface {
            public function getCustomTypeColumn(): string
            {
                return 'post_type';
            }

            public function getCustomTypeCode(): string
            {
                return 'product';
            }
        };

        $connection = $this->createStub(\Illuminate\Database\MySqlConnection::class);
        $query = new \Illuminate\Database\Query\Builder(
            $connection,
            new Grammar($this->createStub(Connection::class)),
            new Processor()
        );
        $builder = new PostBuilder($query);

        $this->scope->apply($builder, $model);

        $this->assertEquals('select * where "post_type" = ?', $builder->toSql());
        $this->assertEquals(['product'], $builder->getBindings());
    }

    /**
     * @return void
     */
    public function testExceptionMessageContainsCorrectClassName(): void
    {
        $model = new Post();
        $builder = $this->createStub(Builder::class);

        try {
            $this->scope->apply($builder, $model);
            $this->fail('Expected WpOrmException was not thrown');
        } catch (WpOrmException $e) {
            $this->assertStringContainsString(Post::class, $e->getMessage());
            $this->assertStringContainsString(CustomModelTypeInterface::class, $e->getMessage());
        }
    }
}
