<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\Unit\Models;

use Dbout\WpOrm\Exceptions\CannotOverrideCustomTypeException;
use Dbout\WpOrm\Exceptions\NotAllowedException;
use Dbout\WpOrm\Models\CustomComment;
use PHPUnit\Framework\TestCase;

class CustomCommentTest extends TestCase
{
    /**
     * @throws NotAllowedException
     * @return never
     * @covers CustomComment::setCommentType
     */
    public function testSetCommentTypeTypeException(): never
    {
        $model = new class () extends CustomComment {
            protected string $_type = 'woocommerce';
        };

        $model = new $model();
        $this->expectException(CannotOverrideCustomTypeException::class);
        $this->expectExceptionMessage('You cannot override type for this object. Current type [woocommerce]');
        $model->setCommentType('my_type');
    }

    /**
     * @return void
     * @covers CustomComment::getCommentType
     */
    public function testSetCommentTypeInConstructor(): void
    {
        $model = new class () extends CustomComment {
            protected string $_type = 'woocommerce';
        };

        $model = new $model([
            'comment_type' => 'application',
        ]);

        $this->assertEquals('woocommerce', $model->getCommentType());
    }

    /**
     * @return void
     * @covers CustomComment::setAttribute
     */
    public function testSetCommentTypeWithSetAttribute(): void
    {
        $model = new class () extends CustomComment {
            protected string $_type = 'woocommerce';
        };

        $model = new $model();

        $this->expectException(CannotOverrideCustomTypeException::class);
        $this->expectExceptionMessage('You cannot override type for this object. Current type [woocommerce]');

        $model->setAttribute('comment_type', 'application');
    }
}
