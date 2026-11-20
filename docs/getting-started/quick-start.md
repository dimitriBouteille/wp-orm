# Quick start

Once [installed](installation.md), every model is ready to use without any configuration. Here are the most common patterns.

## Retrieve a model

```php
use Dbout\WpOrm\Models\Post;
use Dbout\WpOrm\Models\User;

$post = Post::find(42);
$post = Post::findOneByName('hello-world');

$user = User::findOneByEmail('john@example.com');
```

## Query with the builder

```php
use Dbout\WpOrm\Enums\PostStatus;
use Dbout\WpOrm\Models\Post;

$publishedPosts = Post::query()
    ->whereStatus(PostStatus::Publish)
    ->whereTypes('post', 'page')
    ->orderBy(Post::DATE, 'desc')
    ->limit(10)
    ->get();
```

See [Querying models](../models/querying.md) for all the available filters.

## Create or update a model

```php
use Dbout\WpOrm\Models\Post;

$post = new Post();
$post->setPostTitle('Hello world');
$post->setPostName('hello-world');
$post->setPostType('post');
$post->save();

$post->setPostTitle('Hello, again');
$post->save();
```

Every column can be read and written with a magic getter / setter: `setPostTitle()` writes the `post_title` column, `getPostTitle()` reads it. The column constants (`Post::TITLE`, `Post::STATUS`, ...) are also available, see [Built-in models](../models/builtin-models.md).

> [!WARNING]
> The models write directly in the database, the WordPress hooks (`save_post`, `wp_insert_post_data`, ...) are not triggered and the WordPress object cache is not updated. Use the WordPress functions (`wp_insert_post()`, ...) when you need them.

## Work with metas

```php
$post->setMeta('color', 'blue');
$value = $post->getMetaValue('color');     // 'blue'
$post->hasMeta('color');                    // true
$post->deleteMeta('color');

// Filter posts by meta value
Post::query()
    ->addMetaToFilter('color', 'blue')
    ->addMetaToSelect('size')
    ->get();
```

See [Metas](../models/metas.md) for meta casting and the meta filters.

## Use relations

```php
$post = Post::find(42);

$author = $post->author;        // BelongsTo User
$comments = $post->comments;    // HasMany Comment
$parent = $post->parent;        // BelongsTo Post (self)
```

## Manage your tables with migrations

```php
// wp-content/mu-plugins/migrations.php
use Dbout\WpOrm\Migrations\Migrations;

Migrations::enable([
    'paths' => [__DIR__ . '/database/migrations'],
]);
```

```bash
wp orm make:migration create_orders_table
wp orm migrate
```

The migration system is optional and requires `illuminate/filesystem` and [WP-CLI](https://wp-cli.org), see [Migrations](../database/migrations.md).

## And everything else

For everything else (eager loading, scopes, collections, casts...), see [the Eloquent documentation](https://laravel.com/docs/13.x/eloquent): the Eloquent features work out of the box, except the few listed in [Limitations](../advanced/limitations.md).
