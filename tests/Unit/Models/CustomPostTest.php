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
use Dbout\WpOrm\Models\CustomPost;
use PHPUnit\Framework\TestCase;

class CustomPostTest extends TestCase
{
    /**
     * @throws NotAllowedException
     * @return never
     * @covers CustomPost::setPostType
     */
    public function testSetPostTypeException(): never
    {
        $model = new class () extends CustomPost {
            protected string $_type = 'product';
        };

        $this->expectException(CannotOverrideCustomTypeException::class);
        $this->expectExceptionMessage('You cannot override type for this object. Current type [product]');
        $model->setPostType('my_type');
    }

    /**
     * @return void
     * @covers CustomPost::getPostType
     */
    public function testSetPostTypeInConstructor(): void
    {
        $model = new class () extends CustomPost {
            protected string $_type = 'product';
        };

        $model = new $model([
            'post_type' => 'my_type',
        ]);

        $this->assertEquals('product', $model->getPostType());
    }

    /**
     * @return void
     * @covers CustomPost::setAttribute
     */
    public function testSetPostTypeWithSetAttribute(): void
    {
        $model = new class () extends CustomPost {
            protected string $_type = 'product';
        };

        $model = new $model();
        $model->setAttribute('post_type', 'architect');
        $this->assertEquals('product', $model->getPostType());
    }
}