<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\Unit\Scopes;

use Dbout\WpOrm\Builders\OptionBuilder;
use Dbout\WpOrm\Builders\PostBuilder;
use Dbout\WpOrm\Exceptions\WpOrmException;
use Dbout\WpOrm\Models\Attachment;
use Dbout\WpOrm\Models\Option;
use Dbout\WpOrm\Scopes\CustomModelTypeScope;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

#[CoversClass(CustomModelTypeScope::class)]
#[CoversFunction('apply')]
class CustomModelTypeScopeTest extends TestCase
{
    private CustomModelTypeScope $subject;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->subject = new CustomModelTypeScope();
    }

    /**
     * @throws WpOrmException
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @return void
     */
    public function testWithInvalidModel(): void
    {
        $model = new Option();
        $builder = $this->createMock(OptionBuilder::class);
        $this->expectException(WpOrmException::class);
        $this->subject->apply($builder, $model);
    }

    /**
     * @throws WpOrmException
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @return void
     */
    public function testBuilderContainFilter(): void
    {
        $model = new Attachment();
        $query = new \Illuminate\Database\Query\Builder(
            $this->createMock(\Illuminate\Database\MySqlConnection::class),
            new \Illuminate\Database\Query\Grammars\Grammar(),
            new \Illuminate\Database\Query\Processors\Processor()
        );
        $builder = new PostBuilder($query);
        $this->subject->apply($builder, $model);

        $this->assertEquals('select * where "post_type" = ?', $builder->toSql());
        $this->assertEquals([
            'attachment',
        ], $builder->getBindings());
    }
}
