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
    private const EXPECTED_POST_TYPE = 'attachment';

    /**
     * @return void
     * @covers ::find
     */
    public function testFindWithValidPostType(): void
    {
        $objectId = self::factory()->post->create([
            'post_type' => self::EXPECTED_POST_TYPE,
        ]);

        $object = Attachment::find($objectId);
        $this->assertInstanceOf(Attachment::class, $object);
        $this->assertEquals(self::EXPECTED_POST_TYPE, $object->getPostType());
        $this->assertEquals($objectId, $object->getId());
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
        $attachment = new Attachment([
            'post_type' => 'product',
        ]);

        $attachment->save();

        $wpPost = get_post($attachment->getId());
        $this->assertEquals(
            self::EXPECTED_POST_TYPE,
            $attachment->getPostType(),
            'The post_type should not be changed in the object.'
        );

        $this->assertEquals(
            self::EXPECTED_POST_TYPE,
            $wpPost->post_type,
            'The post_type saved should not be changed.'
        );
    }
}
