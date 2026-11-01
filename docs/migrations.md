# Migrations

Migrations are like version control for your database: each change of your custom tables is written in a PHP file, committed with your code and applied on every environment with a single command. The system is based on [Laravel migrations](https://laravel.com/docs/migrations) and works with [WP-CLI](https://wp-cli.org).

> [!NOTE]
> The migration system is optional and disabled by default, the rest of the library works without it.

## Installation

1. Install `illuminate/filesystem`, used to read the migration files:

```bash
composer require illuminate/filesystem ^13.0
```

2. Make sure [WP-CLI](https://wp-cli.org) is available, either installed globally or with Composer:

```bash
composer require --dev wp-cli/wp-cli-bundle
```

3. Enable the migration system:

```php
use Dbout\WpOrm\Migrations\Migrations;

Migrations::enable([
    'paths' => [__DIR__ . '/database/migrations'],
]);
```

`Migrations::enable()` must be called on every request, the simplest is a [must-use plugin](https://developer.wordpress.org/advanced-administration/plugins/mu-plugins/) (e.g. `wp-content/mu-plugins/migrations.php`). The call is cheap: it only checks the configuration and registers the WP-CLI commands when WP-CLI is running, nothing is done on a classic HTTP request.

> [!WARNING]
> `Migrations::enable()` can only be called once, a second call throws a `Dbout\WpOrm\Exceptions\MigrationException`. If several plugins ship migrations, enable the system in a single place with all the paths.

## Configuration

| Option  | Type       | Default          | Description                                                                                                                                                                     |
|---------|------------|------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `paths` | `string[]` | _required_       | Directories of the migration files. Use absolute paths, a relative path depends on the directory where `wp` is run.                                                             |
| `table` | `string`   | `orm_migrations` | Table that stores the migrations that ran, without the WordPress prefix (`wp_orm_migrations` with the default prefix). Letters, numbers and underscores only, 64 characters max including the WordPress prefix. |

```php
Migrations::enable([
    'paths' => [
        WPMU_PLUGIN_DIR . '/database/migrations',
        WP_PLUGIN_DIR . '/my-shop/migrations',
    ],
    'table' => 'app_migrations',
]);
```

An unknown option or an invalid value throws a `Dbout\WpOrm\Exceptions\MigrationException`.

## Create a migration

Use the `make:migration` command to generate a migration file in the first configured path:

```bash
wp orm make:migration create_orders_table
```

The file is named with the current date (e.g. `2026_11_01_143712_create_orders_table.php`), so the migrations always run in the order they were created, even across several paths.

The table is guessed from the name: `create_orders_table` creates the `orders` table and `add_status_to_orders_table` updates it. You can also give it explicitly:

```bash
# Create the "shop_orders" table
wp orm make:migration init_shop --create=shop_orders

# Update the "shop_orders" table
wp orm make:migration add_status --table=shop_orders

# Create the file in another configured path
wp orm make:migration create_orders_table --dir=wp-content/plugins/my-shop/migrations
```

> [!NOTE]
> The option is named `--dir` and not `--path` because `--path` is a global WP-CLI option (path of the WordPress install).

## Write a migration

A migration is an anonymous class that extends `Dbout\WpOrm\Migrations\Migration` with two methods: `up` applies the change and `down` reverts it. Use `$this->schema()` to get the [schema builder](https://laravel.com/docs/migrations#tables), all the Laravel [column types](https://laravel.com/docs/migrations#available-column-types), [indexes](https://laravel.com/docs/migrations#indexes) and [foreign keys](https://laravel.com/docs/migrations#foreign-key-constraints) are available.

```php
use Dbout\WpOrm\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class () extends Migration {
    public function up(): void
    {
        $this->schema()->create('orders', function (Blueprint $table): void {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('post_id')->nullable()->constrained('posts', 'ID')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->schema()->dropIfExists('orders');
    }
};
```

Table names are written **without the WordPress prefix**, it is added automatically: the example above creates `wp_orders`, with a foreign key to `wp_posts`. The tables use the charset and collation of the WordPress database.

> [!WARNING]
> Do not use the Laravel `Schema` facade, it requires a Laravel application. Always use `$this->schema()`.

## Run the migrations

```bash
# Run all the pending migrations
wp orm migrate

# Run each migration in its own batch, so they can be rolled back one by one
wp orm migrate --step
```

The migrations table is created on the first run. The migrations that run together form a **batch**.

## Roll back

```bash
# Roll back the last batch
wp orm migrate:rollback

# Roll back the last 3 migrations
wp orm migrate:rollback --step=3

# Roll back a specific batch
wp orm migrate:rollback --batch=2

# Roll back all the migrations
wp orm migrate:reset
```

These commands ask for a confirmation, use `--yes` to skip it (e.g. in a deployment script).

If the file of a migration that ran does not exist anymore (file deleted or renamed, plugin removed...), the migration is skipped with a warning and stays in the migrations table.

## Migration status

```bash
wp orm migrate:status
```

```
+-----------------------------------------------+-------+---------+
| migration                                     | batch | status  |
+-----------------------------------------------+-------+---------+
| 2026_11_01_143712_create_orders_table         | 1     | Ran     |
| 2026_11_02_091500_add_status_to_orders_table  |       | Pending |
+-----------------------------------------------+-------+---------+
```

The `--format` option accepts `table` (default), `csv`, `json` and `yaml`.

## Events

The migrator dispatches the [Laravel migration events](https://laravel.com/docs/migrations#events) (`MigrationsStarted`, `MigrationStarted`, `MigrationEnded`, `MigrationSkipped`, `MigrationsEnded`, `NoPendingMigrations`...) and a `Dbout\WpOrm\Migrations\Events\MigrationNotFound` event when the file of a migration to roll back is missing. Listen to them with the event dispatcher of the migration system:

```php
use Dbout\WpOrm\Migrations\Migrations;
use Illuminate\Database\Events\MigrationsEnded;

Migrations::getEventDispatcher()->listen(MigrationsEnded::class, function (MigrationsEnded $event): void {
    wp_cache_flush();
});
```

## Limitations

- **No transaction**: MySQL commits each schema change (`CREATE`, `ALTER`, `DROP`...) immediately. If a migration fails halfway, the changes made before the failure are kept and the migration is not marked as ran: fix it and make sure it can run again (e.g. with `hasTable()` / `hasColumn()` checks), or revert the changes by hand.
- **No multisite support**: the tables are created with the prefix of the current site. Multisite is not supported by the library yet ([#170](https://github.com/dimitriBouteille/wp-orm/issues/170)).
- **WP-CLI only**: the migrations cannot be run from the WordPress admin or on plugin activation.
- **No `migrate:fresh` / `migrate:refresh`**: these commands would drop all the tables of the database, including the WordPress ones.
