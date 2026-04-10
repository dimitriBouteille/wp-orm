<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Dbout\WpOrm\Tests\WordPress\Taps\Attachment;

use Dbout\WpOrm\Models\Attachment;
use Dbout\WpOrm\Taps\Attachment\IsMimeTypeTap;
use Dbout\WpOrm\Tests\WordPress\TestCase;

class IsMimeTypeTapTest extends TestCase
{
    private const MIME_JPEG = 'image/jpeg';

    /**
     * @return void
     * @covers IsMimeTypeTap::__construct
     * @covers IsMimeTypeTap::__invoke
     */
    public function testFiltersByImageJpegMimeType(): void
    {
        $jpegId = self::factory()->post->create([
            'post_type' => 'attachment',
            'post_mime_type' => self::MIME_JPEG,
        ]);

        self::factory()->post->create([
            'post_type' => 'attachment',
            'post_mime_type' => 'image/png',
        ]);

        self::factory()->post->create([
            'post_type' => 'attachment',
            'post_mime_type' => 'video/mp4',
        ]);

        $attachments = Attachment::query()
            ->tap(new IsMimeTypeTap(self::MIME_JPEG))
            ->get();

        /** @var Attachment $first */
        $first = $attachments->first();

        $this->assertCount(1, $attachments->toArray());
        $this->assertEquals(self::MIME_JPEG, $first->getPostMimeType());
        $this->assertEquals($jpegId, $first->getId());
    }

    /**
     * @return void
     * @covers IsMimeTypeTap::__invoke
     */
    public function testReturnsEmptyCollectionWhenNoAttachmentsMatch(): void
    {
        self::factory()->post->create([
            'post_type' => 'attachment',
            'post_mime_type' => self::MIME_JPEG,
        ]);

        self::factory()->post->create([
            'post_type' => 'attachment',
            'post_mime_type' => 'image/png',
        ]);

        $attachments = Attachment::query()
            ->tap(new IsMimeTypeTap('video/webm'))
            ->get();

        $this->assertCount(0, $attachments->toArray());
    }

    /**
     * @return void
     * @covers IsMimeTypeTap::__invoke
     */
    public function testCanBeChainedWithOtherQueryMethods(): void
    {
        self::factory()->post->create([
            'post_type' => 'attachment',
            'post_mime_type' => self::MIME_JPEG,
            'post_title' => 'First JPEG',
        ]);

        $secondJpegId = self::factory()->post->create([
            'post_type' => 'attachment',
            'post_mime_type' => self::MIME_JPEG,
            'post_title' => 'Second JPEG',
        ]);

        self::factory()->post->create([
            'post_type' => 'attachment',
            'post_mime_type' => self::MIME_JPEG,
        ]);

        $attachments = Attachment::query()
            ->tap(new IsMimeTypeTap(self::MIME_JPEG))
            ->where('post_title', 'Second JPEG')
            ->get();

        /** @var Attachment $first */
        $first = $attachments->first();

        $this->assertCount(1, $attachments->toArray());
        $this->assertEquals($secondJpegId, $first->getId());
        $this->assertEquals(self::MIME_JPEG, $first->getPostMimeType());
        $this->assertEquals('Second JPEG', $first->getPostTitle());
    }

    /**
     * @return void
     * @covers IsMimeTypeTap::__invoke
     */
    public function testExcludesNonAttachmentPosts(): void
    {
        $attachmentId = self::factory()->post->create([
            'post_type' => 'attachment',
            'post_mime_type' => self::MIME_JPEG,
        ]);

        self::factory()->post->create([
            'post_type' => 'post',
        ]);

        $attachments = Attachment::query()
            ->tap(new IsMimeTypeTap(self::MIME_JPEG))
            ->get();

        /** @var Attachment $first */
        $first = $attachments->first();

        // Should only return the attachment, not the regular post
        $this->assertCount(1, $attachments->toArray());
        $this->assertEquals($attachmentId, $first->getId());
    }

    /**
     * @return void
     * @covers IsMimeTypeTap::__invoke
     */
    public function testGeneratesCorrectSqlQuery(): void
    {
        Attachment::query()
            ->tap(new IsMimeTypeTap(self::MIME_JPEG))
            ->get();

        $this->assertLastQueryEquals(
            "select `#TABLE_PREFIX#posts`.* from `#TABLE_PREFIX#posts` where `post_mime_type` = 'image/jpeg' and `post_type` = 'attachment'"
        );
    }
}
