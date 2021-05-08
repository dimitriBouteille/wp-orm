# Migration with Bedrock :

If you are using the [Bedrock](https://roots.io/bedrock/) framework, please create a file named `config-phinx.php` at the root of your project and copy the following content. Remember to replace the following values :

- {YOUR_WP_DIR} : Wordpress directory
- {YOUR_MIGRATIONS_DIR} : Folder where migrations are saved

```php
<?php

/**
 * Load add_filter function
 * Force to disable maintenance mode
 */
require '{YOUR_WP_DIR}/wp-includes/plugin.php';
add_filter('enable_maintenance_mode', function () {
    return false;
});

/**
 * And load wordpress
 */
require '{YOUR_WP_DIR}/wp-load.php';

/**
 * Export phinx config
 */
return wp_orm_get_phinx_config([
    'migrations_path' => '{YOUR_MIGRATIONS_DIR}',
    'db_user' => DB_USER,
    'db_password' => DB_PASSWORD,
    'db_name' => DB_NAME,
    'db_host' => DB_HOST,
]);
``