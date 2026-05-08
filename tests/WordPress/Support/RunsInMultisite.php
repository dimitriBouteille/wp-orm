<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress\Support;

/**
 * Test case helpers for running against a WordPress multisite install.
 *
 * The package itself does not currently support multisite (cf. README and
 * the v6 milestone). This trait is the test-side scaffolding so that v6
 * work can focus on the ORM layer and not on test plumbing.
 *
 * Usage:
 *
 *     class FooMultisiteTest extends TestCase
 *     {
 *         use RunsInMultisite;
 *
 *         public function testSomething(): void
 *         {
 *             // Auto-skipped when the suite is run without WP_MULTISITE=1.
 *             $value = $this->inBlog($this->createSubsiteId(), function () {
 *                 return get_option('blogname');
 *             });
 *             $this->assertSame('subsite', $value);
 *         }
 *     }
 */
trait RunsInMultisite
{
    /**
     * Skip the test when the WordPress test suite is not booted in
     * multisite mode (set WP_MULTISITE=1 or define WP_TESTS_MULTISITE).
     *
     * @before
     * @return void
     */
    protected function skipIfNotMultisite(): void
    {
        if (!is_multisite()) {
            $this->markTestSkipped('Multisite-only test — run with WP_MULTISITE=1.');
        }
    }

    /**
     * @param int $blogId
     * @return void
     */
    protected function switchToBlog(int $blogId): void
    {
        switch_to_blog($blogId);
    }

    /**
     * @return void
     */
    protected function restoreBlog(): void
    {
        restore_current_blog();
    }

    /**
     * Run the callback inside a switched-blog context, restoring the
     * previous blog regardless of whether the callback throws. Prefer
     * this form over manual switch / restore — it can't leak state.
     *
     * @template T
     * @param int $blogId
     * @param \Closure(): T $callback
     * @return T
     */
    protected function inBlog(int $blogId, \Closure $callback): mixed
    {
        $this->switchToBlog($blogId);
        try {
            return $callback();
        } finally {
            $this->restoreBlog();
        }
    }
}
