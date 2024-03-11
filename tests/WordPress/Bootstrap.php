<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\WordPress;

class Bootstrap
{
    private static ?self $instance = null;

    protected string $wpDirectory = '';

    /**
     * Directory where wordpress-tests-lib is installed.
     * @var string
     */
    protected string $wpTestsDir = '';

    public function __construct()
    {
        $this->wpTestsDir = getenv( 'WP_TESTS_DIR' )
            ? getenv( 'WP_TESTS_DIR' )
            : sys_get_temp_dir() . '/wordpress-tests-lib';

        /**
         * Load PHPUnit Polyfills for the WP testing suite.
         * @see https://github.com/WordPress/wordpress-develop/pull/1563/
         */
        define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', __DIR__ . '/../../vendor/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php' );

        /**
         * Load the WP testing environment.
         */
        require_once $this->wpTestsDir . '/includes/bootstrap.php';

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
