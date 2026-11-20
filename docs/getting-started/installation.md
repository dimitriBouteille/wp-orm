# Installation

## Requirements

This package targets a stricter runtime than [WordPress itself](https://wordpress.org/about/requirements/):

- PHP >= 8.3
- WordPress >= 6.3
- [Composer](https://getcomposer.org/)

The package version follows the version of [Eloquent](https://laravel.com/docs/13.x/eloquent) it is built on: `13.x` uses `illuminate/database` `^13.0`.

> [!TIP]
> To simplify the integration of this library, we recommend using WordPress with one of the following tools: [Bedrock](https://roots.io/bedrock/), [Themosis](https://framework.themosis.com/) or [Wordplate](https://github.com/wordplate/wordplate#readme).

## Install the package

Install the package with [Composer](https://getcomposer.org/):

```bash
composer require dbout/wp-orm
```

Then make sure the Composer autoloader is loaded, for example in your `wp-config.php`:

```php
require __DIR__ . '/vendor/autoload.php';
```

🎉 That's all, the library is ready to use. There is no database configuration: the library uses the WordPress connection (the global `$wpdb` object), with the same credentials, charset, collation and table prefix.

> [!NOTE]
> The models can be used as soon as WordPress has created `$wpdb`, i.e. anywhere in a must-use plugin, a plugin or a theme. Using a model earlier (e.g. directly in `wp-config.php`) throws an exception.

## Optional features

Some features need extra packages:

| Feature                                          | Requirements                                                             |
|--------------------------------------------------|--------------------------------------------------------------------------|
| [Migrations](../database/migrations.md)          | `illuminate/filesystem` and [WP-CLI](https://wp-cli.org)                 |
| [`DB` facade](../database/connection.md#db-facade) | A container, see [Database connection](../database/connection.md#db-facade) |

## Next steps

- [Quick start](quick-start.md): the most common patterns in a few examples.
- [Built-in models](../models/builtin-models.md): the models available for the WordPress tables.
