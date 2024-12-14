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
use Dbout\WpOrm\Models\CustomComment;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

#[CoversClass(CustomComment::class)]
#[CoversFunction('setCommentType')]
#[CoversFunction('getCommentType')]
#[CoversFunction('setAttribute')]
class CustomCommentTest extends TestCase
{
    /**
     * @throws NotAllowedException
     * @return never
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
     */
    public function testSetCommentTypeWithSetAttribute(): void
    {
        $model = new class () extends CustomComment {
            protected string $_type = 'woocommerce';
        };

        $model = new $model();
        $model->setAttribute('comment_type', 'application');
        $this->assertEquals('woocommerce', $model->getCommentType());
    }
}
