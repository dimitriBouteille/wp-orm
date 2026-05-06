<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpOrm\Tests\WordPress;

class Bootstrap
{
    private const VENDOR_DIR = __DIR__ . '/../../vendor';
    private static ?self $instance = null;

    public function __construct()
    {
        $this->checkComposerInstalled();

        /**
         * Set the path to the wp-tests-config.php file.
         * wp-phpunit reads this as a PHP constant, not an environment variable.
         */
        if (!defined('WP_TESTS_CONFIG_FILE_PATH')) {
            define('WP_TESTS_CONFIG_FILE_PATH', __DIR__ . '/wp-tests-config.php');
        }

        /**
         * Enable wpdb query logging so tests can introspect $wpdb->last_query
         * and $wpdb->queries regardless of the order in which test classes run.
         * @see https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/#savequeries
         */
        if (!defined('SAVEQUERIES')) {
            define('SAVEQUERIES', true);
        }

        /**
         * Load PHPUnit Polyfills for the WP testing suite.
         * @see https://github.com/WordPress/wordpress-develop/pull/1563/
         */
        require_once sprintf('%s/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php', self::VENDOR_DIR);

        /**
         * Load the WordPress test suite bootstrap (wp-phpunit).
         */
        require_once sprintf('%s/wp-phpunit/wp-phpunit/includes/bootstrap.php', self::VENDOR_DIR);

        /**
         * This function has to be called _last_, after the WP test bootstrap to make sure it registers
         * itself in FRONT of the Composer autoload (which also prepends itself to the autoload queue).
         */
        require_once sprintf('%s/autoload.php', self::VENDOR_DIR);
    }

    /**
     * @return void
     */
    protected function checkComposerInstalled(): void
    {
        $path = sprintf('%s/autoload.php', self::VENDOR_DIR);
        if (!@file_exists($path)) {
            echo PHP_EOL, 'ERROR: Run `composer install` to install the dependencies',
            ' before running the tests.', PHP_EOL;
            exit(1);
        }
    }

    /**
     * @return self
     */
    public static function run(): self
    {
        if (!self::$instance instanceof Bootstrap) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

Bootstrap::run();
