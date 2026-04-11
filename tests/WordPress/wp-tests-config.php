<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */
/** Path to the WordPress codebase (installed by roots/wordpress) */
define('ABSPATH', dirname(__DIR__, 2) . '/web/wordpress/');

/** Test database settings */
define('DB_NAME', getenv('MYSQL_DATABASE') ?: 'wordpress_test');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASSWORD', getenv('MYSQL_ROOT_PASSWORD') ?: 'root');
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1:3307');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

/** WordPress test suite settings */
define('WP_TESTS_DOMAIN', 'example.org');
define('WP_TESTS_EMAIL', 'admin@example.org');
define('WP_TESTS_TITLE', 'Test Blog');
define('WP_PHP_BINARY', 'php');

/** WordPress language */
define('WPLANG', '');

$table_prefix = 'wptests_';
