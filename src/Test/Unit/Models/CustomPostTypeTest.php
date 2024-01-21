<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Test\Unit\Models;

use Dbout\WpOrm\Exceptions\CannotOverrideCustomTypeException;
use Dbout\WpOrm\Models\Attachment;
use PHPUnit\Framework\TestCase;

/**
 * @since 3.0.0
 */
class CustomPostTypeTest extends TestCase
{
    /**
     * @throws \Dbout\WpOrm\Exceptions\NotAllowedException
     * @return void
     */
    public function testSetPostTypeException(): void
    {
        $model = new Attachment();
        $this->expectException(CannotOverrideCustomTypeException::class);
        $this->expectExceptionMessage('You cannot override type for this object. Current type [attachment]');
        $model->setPostType('my_type');
    }

    /**
     * @return void
     */
    public function testSetPostTypeInConstructor(): void
    {
        $model = new Attachment([
            'post_type' => 'my_type',
        ]);

        $this->assertEquals('attachment', $model->getPostType());
    }
}
