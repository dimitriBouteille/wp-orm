<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress\Models;

use Dbout\WpOrm\Models\Attachment;
use Dbout\WpOrm\Tests\WordPress\TestCase;

/**
 * @coversDefaultClass \Dbout\WpOrm\Models\CustomPost
 */
class CustomPostTypeTest extends TestCase
{
    /**
     * @return void
     * @covers ::find
     */
    public function testFindWithValidPostType(): void
    {
        $objectId = self::factory()->post->create([
            'post_type' => 'attachment',
        ]);

        $object = Attachment::find($objectId);
        $this->assertInstanceOf(Attachment::class, $object);
        $this->assertEquals('attachment', $object->getPostType());
        $this->assertEquals($objectId, $object->getId());

        global $wpdb;
        $table = $wpdb->prefix . 'posts';
        $expectedQuery = sprintf(
            "select `%s`.* from `%s` where `ID` = '%s'",
            $table,
            $table,
            $object->getId()
        );

        $this->assertLastQueryEqual($expectedQuery);
    }

    /**
     * @return void
     * @covers ::find
     */
    public function testFindWithAnotherPostType(): void
    {
        $objectId = self::factory()->post->create([
            'post_type' => 'product',
        ]);

        $object = Attachment::find($objectId);
        $this->assertNull($object, 'Value must be null because cannot load another post_type object.');
    }

    /**
     * @return void
     * @covers ::save
     */
    public function testSaveWithAnotherPostType(): void
    {
        $expectedPostType = 'attachment';
        $attachment = new Attachment([
            'post_type' => 'product',
        ]);

        $attachment->save();

        $wpPost = get_post($attachment->getId());
        $this->assertEquals(
            $expectedPostType,
            $attachment->getPostType(),
            'The post_type should not be changed in the object.'
        );

        $this->assertEquals(
            $expectedPostType,
            $wpPost->post_type,
            'The post_type saved should not be changed.'
        );
    }
}
