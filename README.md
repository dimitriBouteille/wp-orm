# WordPress ORM with Eloquent

![GitHub Release](https://img.shields.io/github/v/release/dimitriBouteille/wp-orm) [![tests](https://img.shields.io/github/actions/workflow/status/dimitriBouteille/wp-orm/tests.yml?label=tests)](https://github.com/dimitriBouteille/wp-orm/actions/workflows/tests.yml) [![Packagist Downloads](https://img.shields.io/packagist/dt/dbout/wp-orm?color=yellow)](https://packagist.org/packages/dbout/wp-orm) ![Eloquent version](https://img.shields.io/packagist/dependency-v/dbout/wp-orm/illuminate%2Fdatabase?color=orange)

> [!IMPORTANT]
> The phinx package will be removed in a future release in order to use the Laravel migration system. It is therefore advisable to stop using the tool. [More info](https://github.com/dimitriBouteille/wp-orm/issues/27).

WordPress ORM with Eloquent is a small library that adds a basic ORM into WordPress, which is easily extendable and includes models for core WordPress models such as posts, post metas, users, comments and more.
The ORM is based on [Eloquent ORM](https://laravel.com/docs/eloquent) and uses the WordPress connection (`wpdb` class).

> 💡 To simplify the integration of this library, we recommend using WordPress with one of the following tools: [Bedrock](https://roots.io/bedrock/), [Themosis](https://framework.themosis.com/) or [Wordplate](https://github.com/wordplate/wordplate#readme).

### Features

- ✅ Support core WordPress models: `Comment`, `Option`, `Post`, `TermTaxonomy`, `Term`, `User`, `PostMeta` and `UserMeta`
- ✅ Support core WordPress post type: `Article`, `Attachment` and `Page`
- ✅ Based on core WordPress database connection (`wpdb` class), no configuration required !
- ✅ Custom functions to filter models with meta
- ❤️ Easy integration of a custom post type
- ❤️ Easy model creation for projects with custom tables
- ❤️ All the features available in Eloquent, are usable with this library !

**Not yet developed but planned in a future version:**

- 🗓️ Create custom comment type
- 🗓️ Meta casting (e.g. [Attribute Casting](https://laravel.com/docs/10.x/eloquent-mutators#attribute-casting)) 

### Documentation

This documentation only covers the specific points of this library, if you want to know more about Eloquent, the easiest is to look at [the documentation of Eloquent](https://laravel.com/docs/10.x/eloquent) :)

- [Installation](#installation)
- [Use WordPress core models](doc/wordpress-core-models.md)
- [Filter data](/doc/filter-data.md)
    - [With findOneBy*](/doc/filter-data.md#with-findoneby)
    - [With taps](/doc/filter-data.md#with-taps)
    - [With query builder](/doc/filter-data.md#with-query-builder)
- [Events](/doc/events.md)
- [Create custom model](doc/create-model.md)
    - [Generic Model](doc/create-model.md#generic-model)
    - [Custom Post Type Model](doc/create-model.md#custom-post-type-model)
- [~~Migration with Phinx~~](doc/migration.md)

## Installation

**Requirements**

The server requirements are basically the same as for [WordPress](https://wordpress.org/about/requirements/) with the addition of a few ones :

- PHP >= 8.1
- [Composer](https://getcomposer.org/)

**Installation**

You can use [Composer](https://getcomposer.org/). Follow the [installation instructions](https://getcomposer.org/doc/00-intro.md) if you do not already have composer installed.

~~~bash
composer require dbout/wp-orm
~~~

In your PHP script, make sure you include the autoloader:

~~~php
require __DIR__ . '/vendor/autoload.php';
~~~

🎉 You have nothing more to do, you can use the library now! Not even need to configure database accesses because it's the `wpdb` connection that is used.

## Contributing

We encourage you to contribute to this repository, so everyone can benefit from new features, bug fixes, and any other improvements. Have a look at our [contributing guidelines](CONTRIBUTING.md) to find out how to raise a pull request.