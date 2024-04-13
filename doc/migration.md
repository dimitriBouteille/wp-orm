# Migration

To use phinx, you must create a configuration file named `config-phinx.php` at the root of your project. To create this file, please see one of the following documentation:

- [Create config-phinx.php file for Bedrock Framework](#bedrock-support)

If you would like to learn more about the configuration file, please visit  [Phinx - Configuration](https://phinx.readthedocs.io/en/latest/configuration.html).

#### Create new migration :

~~~bash
php vendor/bin/phinx create -c config-phinx.php
~~~

#### Run migration :

~~~bash
php vendor/bin/phinx migrate -c config-phinx.php
~~~

## Bedrock support

If you are using the [Bedrock](https://roots.io/bedrock/) framework, please create a file named `config-phinx.php` at the root of your project and copy the following content.

~~~php
<?php

// Your WordPress directory
$wpDirectory = 'web/web';

/**
 * Load add_filter function
 * Force to disable maintenance mode
 */
require $wpDirectory . '/wp-includes/plugin.php';
add_filter('enable_maintenance_mode', function () {
    return false;
});

/**
 * And load wordpress
 */
require $wpDirectory . '/wp-load.php';

/**
 * Export phinx config
 */
return wp_orm_get_phinx_config([
    'migrations_path' => 'YOUR_MIGRATIONS_DIR',
    'db_user' => DB_USER,
    'db_password' => DB_PASSWORD,
    'db_name' => DB_NAME,
    'db_host' => DB_HOST,
]);
~~~