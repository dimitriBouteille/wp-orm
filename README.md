# WordPress ORM with Eloquent

[![GitHub Release](https://img.shields.io/github/v/release/dimitriBouteille/wp-orm)](https://github.com/dimitriBouteille/wp-orm/releases)
[![Tests](https://img.shields.io/github/actions/workflow/status/dimitriBouteille/wp-orm/tests.yml?label=tests)](https://github.com/dimitriBouteille/wp-orm/actions/workflows/tests.yml)
[![Packagist Downloads](https://img.shields.io/packagist/dt/dbout/wp-orm?color=yellow)](https://packagist.org/packages/dbout/wp-orm)
[![Eloquent version](https://img.shields.io/packagist/dependency-v/dbout/wp-orm/illuminate%2Fdatabase?color=orange)](https://github.com/dimitriBouteille/wp-orm/blob/main/composer.json)
[![Coverage Status](https://coveralls.io/repos/github/dimitriBouteille/wp-orm/badge.svg?branch=main)](https://coveralls.io/github/dimitriBouteille/wp-orm)

WordPress ORM with Eloquent is a small library that adds a basic ORM into WordPress, which is easily extendable and includes models for core WordPress models such as posts, post metas, users, comments and more.
The ORM is based on [Eloquent ORM](https://laravel.com/docs/13.x/eloquent) and uses the WordPress connection (`wpdb` class).

📖 **Documentation: [dimitribouteille.github.io/wp-orm](https://dimitribouteille.github.io/wp-orm/)**

> [!TIP]
> To simplify the integration of this library, we recommend using WordPress with one of the following tools: [Bedrock](https://roots.io/bedrock/), [Themosis](https://framework.themosis.com/) or [Wordplate](https://github.com/wordplate/wordplate#readme).

## At a glance

```php
use Dbout\WpOrm\Enums\PostStatus;
use Dbout\WpOrm\Models\CustomPost;

// A model for your custom post type
class Product extends CustomPost
{
    protected string $_type = 'product';

    protected function metaCasts(): array
    {
        return ['price' => 'float'];
    }
}

// Query it with Eloquent, filter on metas, eager load the relations
$products = Product::query()
    ->whereStatus(PostStatus::Publish)
    ->addMetaToFilter('featured', 'yes')
    ->with('author')
    ->orderBy(Product::DATE, 'desc')
    ->limit(10)
    ->get();

foreach ($products as $product) {
    echo $product->getPostTitle();          // post_title column
    echo $product->getMetaValue('price');   // float, thanks to the meta cast
    echo $product->author->getDisplayName();
}

// Create or update, without SQL
$product = Product::find(42);
$product->setPostTitle('Blue T-shirt');
$product->save();
$product->setMeta('price', 19.90);
```

No configuration is needed: the library uses the WordPress database connection.

## Features

- ✅ Support core WordPress models: `Comment`, `Option`, `Post`, `Term`, `TermTaxonomy`, `TermRelationship`, `User`, `PostMeta` and `UserMeta`
- ✅ Support core WordPress post types: `Article`, `Attachment` and `Page`
- ✅ Based on core WordPress database connection (`wpdb` class), no configuration required
- ✅ Custom functions to filter models with meta
- ✅ Meta casting (e.g. [Attribute Casting](https://laravel.com/docs/13.x/eloquent-mutators#attribute-casting))
- ✅ [Database migrations](https://dimitribouteille.github.io/wp-orm/database/migrations) with WP-CLI (`wp orm migrate`)
- ❤️ Easy integration of a custom post and comment type
- ❤️ Easy model creation for projects with custom tables
- ❤️ All the features available in Eloquent are usable with this library

**Not yet developed but planned in a future version:**

- 🗓️ Multisite support (network-shared tables and `switch_blog()` handling)

## Installation

Requires PHP >= 8.3 and WordPress >= 6.3.

```bash
composer require dbout/wp-orm
```

Make sure the Composer autoloader is loaded (e.g. in your `wp-config.php`), and you are ready to go. See [Installation](https://dimitribouteille.github.io/wp-orm/getting-started/installation) for the details.

## Documentation

The full documentation is available on **[dimitribouteille.github.io/wp-orm](https://dimitribouteille.github.io/wp-orm/)**. It only covers the specific points of this library, everything else works like Eloquent: see [the documentation of Eloquent](https://laravel.com/docs/13.x/eloquent).

- **Getting started:** [Installation](https://dimitribouteille.github.io/wp-orm/getting-started/installation), [Quick start](https://dimitribouteille.github.io/wp-orm/getting-started/quick-start)
- **Models:** [Built-in models](https://dimitribouteille.github.io/wp-orm/models/builtin-models), [Querying models](https://dimitribouteille.github.io/wp-orm/models/querying), [Metas](https://dimitribouteille.github.io/wp-orm/models/metas), [Custom models](https://dimitribouteille.github.io/wp-orm/models/custom-models), [Events](https://dimitribouteille.github.io/wp-orm/models/events)
- **Database:** [Database connection](https://dimitribouteille.github.io/wp-orm/database/connection), [Migrations](https://dimitribouteille.github.io/wp-orm/database/migrations)
- **Advanced:** [Security](https://dimitribouteille.github.io/wp-orm/advanced/security), [Limitations](https://dimitribouteille.github.io/wp-orm/advanced/limitations)

> [!WARNING]
> Every model is mass-assignable by default (`$guarded = []`) and the models bypass the WordPress hooks and object cache. Read [Security](https://dimitribouteille.github.io/wp-orm/advanced/security) and [Limitations](https://dimitribouteille.github.io/wp-orm/advanced/limitations) before using the library with user input.

## Testing

🐞 This project includes two types of tests, both running on PHPUnit 12:

- **Unit tests** - Isolated tests without WordPress dependencies
- **WordPress tests** - Integration tests with WordPress core (uses [`wp-phpunit/wp-phpunit`](https://github.com/wp-phpunit/wp-phpunit))

```bash
# Unit tests
composer run test:unit

# WordPress tests (requires Docker)
./run-wp-tests.sh

# With coverage
./run-wp-tests.sh --coverage
```

See [TESTING.md](TESTING.md) for detailed setup instructions and troubleshooting.

## Contributing

💕 🦄 We encourage you to contribute to this repository, so everyone can benefit from new features, bug fixes, and any other improvements. Have a look at our [contributing guidelines](CONTRIBUTING.md) to find out how to raise a pull request.
