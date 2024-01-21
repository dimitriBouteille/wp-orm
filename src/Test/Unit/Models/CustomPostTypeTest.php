<?php

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
     * @return void
     * @throws \Dbout\WpOrm\Exceptions\NotAllowedException
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
