# Database connection

The library uses the WordPress database connection (the global `$wpdb` object) through `Dbout\WpOrm\Orm\Database`, an Eloquent connection that runs the queries with `$wpdb`. There is nothing to configure: the credentials, charset, collation and table prefix are those of WordPress.

## Query builder

Use `Database::getInstance()` to get the connection and run queries without a model, with the [Laravel query builder](https://laravel.com/docs/13.x/queries):

```php
use Dbout\WpOrm\Orm\Database;

$db = Database::getInstance();

$count = $db->table('posts')
    ->where('post_status', 'publish')
    ->count();

$db->table('options')
    ->where('option_name', 'my_option')
    ->update(['option_value' => 'new value']);
```

As with the models, the table names are written without the WordPress prefix.

The values are escaped with `$wpdb->prepare()`. For raw SQL, use the usual Laravel methods:

```php
$db->select('SELECT ID FROM wp_posts WHERE post_author = ?', [1]);
$db->statement('OPTIMIZE TABLE wp_options');
```

> [!WARNING]
> With raw SQL, the table prefix is not added: use `$db->getTablePrefix()` to build the table names.

## Transactions

```php
use Dbout\WpOrm\Orm\Database;

Database::getInstance()->transaction(function () {
    // ...
});
```

The `beginTransaction()`, `commit()` and `rollBack()` methods are also available. See [Laravel transactions](https://laravel.com/docs/13.x/database#database-transactions).

> [!NOTE]
> The transactions only apply to the tables that support them (InnoDB, the default engine of WordPress). The schema changes (`CREATE`, `ALTER`, `DROP`...) are committed immediately by MySQL.

## Schema builder

`Database::getInstance()->getSchemaBuilder()` returns the [schema builder](https://laravel.com/docs/13.x/migrations#tables), to check or update the structure of the tables:

```php
use Dbout\WpOrm\Orm\Database;

$schema = Database::getInstance()->getSchemaBuilder();

if ($schema->hasTable('orders')) {
    // ...
}
```

To create and update your tables, prefer the [migrations](migrations.md).

## DB facade

The Laravel [facades](https://laravel.com/docs/13.x/facades) need a [service container](https://laravel.com/docs/13.x/container), which is provided by a Laravel application. To use the `DB` facade, register the connection in a container, once, for example in a must-use plugin:

```php
// wp-content/mu-plugins/wp-orm.php
use Dbout\WpOrm\Orm\Database;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;

$container = new Container();
$container->instance('db', Database::getInstance());
Facade::setFacadeApplication($container);
```

You can now use the `DB` facade:

```php
use Illuminate\Support\Facades\DB;

$posts = DB::table('posts')->where('post_type', 'page')->get();
```

> [!NOTE]
> Only the `DB` facade is registered by this example. The other facades (`Cache`, `Schema`...) need other Laravel services and do not work. For the schema, use `getSchemaBuilder()`.
