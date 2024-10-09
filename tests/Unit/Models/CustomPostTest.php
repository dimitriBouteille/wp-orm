<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\Unit\Models;

use Dbout\WpOrm\Exceptions\CannotOverrideCustomTypeException;
use Dbout\WpOrm\Exceptions\NotAllowedException;
use Dbout\WpOrm\Models\CustomPost;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

#[CoversClass(CustomPost::class)]
#[CoversFunction('setPostType')]
#[CoversFunction('getPostType')]
#[CoversFunction('setAttribute')]
class CustomPostTest extends TestCase
{
    /**
     * @throws NotAllowedException
     * @return never
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
