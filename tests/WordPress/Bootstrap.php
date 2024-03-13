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

    /**
     * @var string|null
     */
    protected ?string $wpTestsDir = null;

    public function __construct()
    {
        $this->wpTestsDir = $this->getPathToWpTestDir();

        /**
         * Load WordPress
         */
        $this->initBoostrap();

        /**
         * This function has to be called _last_, after the WP test bootstrap to make sure it registers
         * itself in FRONT of the Composer autoload (which also prepends itself to the autoload queue).
         */
        $this->loadComposerAutoloader();
    }

    /**
     * @param string $path
     * @return string
     */
    private function normalizePath(string $path): string
    {
        return \str_replace('\\', '/', $path);
    }

    /**
     * @return string|null
     */
    protected function getPathToWpTestDir(): ?string
    {
        if (\getenv('WP_TESTS_DIR') !== false) {
            $testsDir = \getenv('WP_TESTS_DIR');
            $testsDir = \realpath($testsDir);

            if ($testsDir !== false) {
                $testsDir = $this->normalizePath($testsDir) . '/';
                if (\is_dir($testsDir) === true
                    && @\file_exists($testsDir . 'includes/bootstrap.php')
                ) {
                    return $testsDir;
                }
            }

            unset($testsDir);
        }

        if (\getenv('WP_DEVELOP_DIR') !== false) {
            $devDir = \getenv('WP_DEVELOP_DIR');
            $devDir = \realpath($devDir);
            if ($devDir !== false) {
                $devDir = $this->normalizePath($devDir) . '/';
                if (\is_dir($devDir) === true
                    && @\file_exists($devDir . 'tests/phpunit/includes/bootstrap.php')
                ) {
                    return $devDir . 'tests/phpunit/';
                }
            }

            unset($devDir);
        }

        /**
         * Last resort: see if this is a typical WP-CLI scaffold command set-up where a subset of
         * the WP test files have been put in the system temp directory.
         */
        $testsDir = \sys_get_temp_dir() . '/wordpress-tests-lib';
        $testsDir = \realpath($testsDir);
        if ($testsDir !== false) {
            $testsDir = $this->normalizePath($testsDir) . '/';
            if (\is_dir($testsDir) === true
                && @\file_exists($testsDir . 'includes/bootstrap.php')
            ) {
                return $testsDir;
            }
        }

        return null;
    }

    /**
     * Load the Composer autoload file.
     *
     * @return void
     */
    protected function loadComposerAutoloader(): void
    {
        $path = __DIR__ . '/../../vendor/autoload.php';
        if (!@file_exists($path)) {
            echo \PHP_EOL, 'ERROR: Run `composer install` or `composer update -W` to install the dependencies',
            ' and generate the autoload files before running the unit tests.', \PHP_EOL;
            exit(1);
        }

        require_once $path;
    }

    /**
     * Loads the WordPress native test bootstrap file to set up the environment.
     *
     * @return void
     */
    protected function initBoostrap(): void
    {
        if ($this->wpTestsDir === null) {
            echo \PHP_EOL, 'ERROR: The WordPress native unit test bootstrap file could not be found. Please set either the WP_TESTS_DIR or the WP_DEVELOP_DIR environment variable, either in your OS or in a custom phpunit.xml file.', \PHP_EOL;
            exit(1);
        }

        /**
         * Load PHPUnit Polyfills for the WP testing suite.
         * @see https://github.com/WordPress/wordpress-develop/pull/1563/
         * @todo Maybe check if composer installed
         */
        //require_once __DIR__ . '/vendor/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php';

        /**
         * We can safely load the bootstrap - already verifies it exists.
         * Load the WP testing environment.
         */
        require_once sprintf('%s/includes/bootstrap.php', rtrim($this->wpTestsDir, '/'));
    }

    /**
     * Loads the WP native integration test bootstrap and register a custom autoloader.
     *
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
