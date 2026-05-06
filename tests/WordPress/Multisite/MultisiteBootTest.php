<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Multisite;

use Dbout\WpOrm\Tests\WordPress\Support\RunsInMultisite;
use Dbout\WpOrm\Tests\WordPress\TestCase;

/**
 * Smoke test that proves the suite is running in multisite mode when the
 * dedicated CI job kicks in. Real ORM-level multisite coverage will land
 * with the v6 multisite implementation.
 *
 * @group multisite
 */
class MultisiteBootTest extends TestCase
{
    use RunsInMultisite;

    /**
     * @return void
     * @coversNothing
     */
    public function testSuiteBootsInMultisite(): void
    {
        $this->assertTrue(is_multisite());
        $this->assertTrue(function_exists('switch_to_blog'));
        $this->assertTrue(function_exists('restore_current_blog'));
    }

    /**
     * @return void
     * @coversNothing
     */
    public function testInBlogRestoresCurrentBlogIdEvenOnException(): void
    {
        $initialBlogId = get_current_blog_id();
        $caught = null;

        try {
            $this->inBlog($initialBlogId, function (): void {
                throw new \RuntimeException('test boundary');
            });
        } catch (\RuntimeException $e) {
            $caught = $e;
        }

        $this->assertNotNull($caught, 'Expected exception to propagate.');
        $this->assertSame($initialBlogId, get_current_blog_id());
    }
}
