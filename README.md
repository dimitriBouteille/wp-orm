# WordPress ORM with Eloquent

[![GitHub Release](https://img.shields.io/github/v/release/dimitriBouteille/wp-orm)](https://github.com/dimitriBouteille/wp-orm/releases)
[![Tests](https://img.shields.io/github/actions/workflow/status/dimitriBouteille/wp-orm/tests.yml?label=tests)](https://github.com/dimitriBouteille/wp-orm/actions/workflows/tests.yml)
[![Packagist Downloads](https://img.shields.io/packagist/dt/dbout/wp-orm?color=yellow)](https://packagist.org/packages/dbout/wp-orm)
[![Eloquent version](https://img.shields.io/packagist/dependency-v/dbout/wp-orm/illuminate%2Fdatabase?color=orange)](https://github.com/dimitriBouteille/wp-orm/blob/main/composer.json)
[![Coverage Status](https://coveralls.io/repos/github/dimitriBouteille/wp-orm/badge.svg?branch=main)](https://coveralls.io/github/dimitriBouteille/wp-orm)

WordPress ORM with Eloquent is a small library that adds a basic ORM into WordPress, which is easily extendable and includes models for core WordPress models such as posts, post metas, users, comments and more.
The ORM is based on [Eloquent ORM](https://laravel.com/docs/eloquent) and uses the WordPress connection (`wpdb` class).

> [!TIP]
> To simplify the integration of this library, we recommend using WordPress with one of the following tools: [Bedrock](https://roots.io/bedrock/), [Themosis](https://framework.themosis.com/) or [Wordplate](https://github.com/wordplate/wordplate#readme).

## Features

- ✅ Support core WordPress models: `Comment`, `Option`, `Post`, `Term`, `TermTaxonomy`, `TermRelationship`, `User`, `PostMeta` and `UserMeta`
- ✅ Support core WordPress post types: `Article`, `Attachment` and `Page`
- ✅ Based on core WordPress database connection (`wpdb` class), no configuration required
- ✅ Custom functions to filter models with meta
- ✅ Meta casting (e.g. [Attribute Casting](https://laravel.com/docs/eloquent-mutators#attribute-casting))
- ❤️ Easy integration of a custom post and comment type
- ❤️ Easy model creation for projects with custom tables
- ❤️ All the features available in Eloquent are usable with this library

**Not yet developed but planned in a future version:**

- 🗓️ [Create migration tool with Eloquent](https://github.com/dimitriBouteille/wp-orm/issues/28)
- 🗓️ Multisite support (network-shared tables and `switch_blog()` handling)

## Documentation

This documentation only covers the specific points of this library, if you want to know more about Eloquent, the easiest is to look at [the documentation of Eloquent](https://laravel.com/docs/eloquent).

You can find all the documentation in [the wiki](https://github.com/dimitriBouteille/wp-orm/wiki).

## Installation

**Requirements**

This package targets a stricter runtime than [WordPress itself](https://wordpress.org/about/requirements/):

- PHP >= 8.3
- WordPress >= 6.3
- [Composer](https://getcomposer.org/)

**Installation**

You can use [Composer](https://getcomposer.org/). Follow the [installation instructions](https://getcomposer.org/doc/00-intro.md) if you do not already have composer installed.

```bash
composer require dbout/wp-orm
```

In your `wp-config.php` make sure you include the autoloader:

```php
require __DIR__ . '/vendor/autoload.php';
```

🎉 You have nothing more to do, you can use the library now. No need to configure database accesses because the `wpdb` connection is used.

## Quick start

Once installed, every model is ready to use without any configuration. Here are the most common patterns:

**Retrieve a model**

```php
use Dbout\WpOrm\Models\Post;
use Dbout\WpOrm\Models\User;

$post = Post::find(42);
$post = Post::findOneByName('hello-world');

$user = User::findOneByEmail('john@example.com');
```

**Query with the builder**

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

**Create or update a model**

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

**Work with metas**

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

**Use relations**

```php
$post = Post::find(42);

$author = $post->author;        // BelongsTo User
$comments = $post->comments;    // HasMany Comment
$parent = $post->parent;        // BelongsTo Post (self)
```

For everything else (eager loading, scopes, transactions, casts…), see [the Eloquent documentation](https://laravel.com/docs/eloquent) — every Eloquent feature works out of the box.

## Security notes

> [!WARNING]
> **Mass assignment is wide open by default.**
> Every model inherits `protected $guarded = []`, which means **every column is mass-assignable**. A call like `User::create($_POST)` would let a caller set sensitive fields such as `user_pass`. When you accept user input, always pre-validate it or override `$fillable` / `$guarded` on the model:
>
> ```php
> class SafeUser extends \Dbout\WpOrm\Models\User
> {
>     protected $fillable = [
>         self::LOGIN,
>         self::EMAIL,
>         self::DISPLAY_NAME,
>     ];
> }
> ```

> [!WARNING]
> **Multisite is not supported in this release.**
> The library does not handle network-shared tables or `switch_blog()`. After a `switch_blog()` call, the connection prefix is not refreshed and models targeting shared tables (`User`, `UserMeta`) may produce incorrect queries on subsites. Multisite support is planned for a future release — track [the milestone](https://github.com/dimitriBouteille/wp-orm/issues) for progress.

## Testing

🐞 This project includes two types of tests:

- **Unit tests** - Isolated tests without WordPress dependencies
- **WordPress tests** - Integration tests with WordPress core (uses [`wp-phpunit/wp-phpunit`](https://github.com/wp-phpunit/wp-phpunit))

Both suites run on PHPUnit 12.

**Running tests:**

```bash
# Unit tests
composer run test:unit

# WordPress tests (requires Docker)
./run-wp-tests.sh

# With coverage
./run-wp-tests.sh --coverage
```

**Local setup:**

WordPress tests require Docker and Subversion. The `run-wp-tests.sh` script automatically sets up a MySQL container and installs WordPress test environment. WordPress files are cached in `var/testings/` for faster subsequent runs.

See [TESTING.md](TESTING.md) for detailed setup instructions and troubleshooting.

## Contributing

💕 🦄 We encourage you to contribute to this repository, so everyone can benefit from new features, bug fixes, and any other improvements. Have a look at our [contributing guidelines](CONTRIBUTING.md) to find out how to raise a pull request.
