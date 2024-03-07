<?php
/**
 * Copyright (c) 2024 Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

define('WPINC', 'wp-includes');
define('ABSPATH', __DIR__ . '/../web/wordpress/');
$includeDirectory  = __DIR__ . '/../web/wordpress/wp-includes';

$paths = [
    '/load.php',
    '/functions.php',
    '/plugin.php',
    '/class-wpdb.php',
    '/class-wp-error.php',
];

foreach ($paths as $path) {
    require $includeDirectory . $path;
}
