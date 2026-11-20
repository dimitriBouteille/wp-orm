# Limitations

## WordPress hooks and cache

The models read and write directly in the database:

- The WordPress hooks (`save_post`, `wp_insert_post_data`, `profile_update`, `deleted_post`...) are not triggered.
- The WordPress object cache is not updated: after a change, `get_post()` or `get_option()` may return the old value until the cache is cleared (e.g. with `clean_post_cache()`).
- Deleting a model does not delete its relations (metas, comments, term relationships...).

Use the WordPress functions (`wp_insert_post()`, `update_option()`...) when you need these side effects.

## Multisite

Multisite is not supported yet ([#170](https://github.com/dimitriBouteille/wp-orm/issues/170)):

- The table prefix is read once, when the connection is created. After a `switch_to_blog()`, the models still use the tables of the first site.
- The models of the network-shared tables (`User`, `UserMeta`) use the prefix of the current site, which may produce incorrect queries on the subsites.

## Metas

- Only `Post` and `User` (and their children) support metas. There is no model for the `wp_commentmeta` and `wp_termmeta` tables yet.
- The metas of a new model are only saved when an [event dispatcher](../models/events.md) is configured.
- The values serialized by WordPress are returned as raw strings, see [Metas](../models/metas.md#serialized-values).

## Attribute casting

Depending on the database driver, the column values may not be cast automatically to their PHP type (e.g. an `INT` column returned as a `string`). Define the [`$casts`](https://laravel.com/docs/13.x/eloquent-mutators#attribute-casting) of your custom models to get the expected types, see [#74](https://github.com/dimitriBouteille/wp-orm/issues/74).

## Eloquent features

The connection runs the queries with `$wpdb` instead of PDO, so a few features are not available:

- `pretend()` throws a `Dbout\WpOrm\Exceptions\WpOrmException`.
- `getPdo()` cannot be used.
- The query log (`enableQueryLog()`, `DB::listen()`) does not record the queries.
- The other Laravel services (facades other than `DB`, `Schema` facade, cache, queues...) need a Laravel application.
