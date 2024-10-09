<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Tests\Unit;

class Bootstrap
{
    private static ?self $instance = null;

    protected string $wpDirectory = '';

    public function __construct()
    {
        $this->wpDirectory = __DIR__ . '/../../web/wordpress';
        $this->initConstants();
        $this->loadFiles();
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

    /**
     * @return void
     */
    protected function initConstants(): void
    {
        define('ABSPATH', sprintf('%s/', $this->wpDirectory));
        define('WP_DEBUG', false);
        define('WP_CONTENT_DIR', '/');
        define('WP_DEBUG_LOG', false);
        define('WP_PLUGIN_DIR', './');
        define('WPMU_PLUGIN_DIR', './');
        define('EMPTY_TRASH_DAYS', 30 * 86400);
        define('SCRIPT_DEBUG', false);
        define('WP_LANG_DIR', './');
        define('WPINC', 'wp-includes');
    }

    /**
     * @return void
     */
    protected function loadFiles(): void
    {
        $paths = [
            'load.php',
            'functions.php',
            'plugin.php',
            'class-wpdb.php',
            'class-wp-error.php',
        ];

        foreach ($paths as $path) {
            require sprintf('%s/wp-includes/%s', $this->wpDirectory, $path);
        }
    }
}

Bootstrap::run();
