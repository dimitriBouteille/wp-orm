# WordPress ORM with Eloquent

![GitHub Release](https://img.shields.io/github/v/release/dimitriBouteille/wp-orm) [![tests](https://img.shields.io/github/actions/workflow/status/dimitriBouteille/wp-orm/tests.yml?label=tests)](https://github.com/dimitriBouteille/wp-orm/actions/workflows/tests.yml) [![Packagist Downloads](https://img.shields.io/packagist/dt/dbout/wp-orm?color=yellow)](https://packagist.org/packages/dbout/wp-orm) ![Eloquent version](https://img.shields.io/packagist/dependency-v/dbout/wp-orm/illuminate%2Fdatabase?color=orange)

WordPress ORM wih Eloquent is a small library that adds a basic ORM into WordPress, which is easily extendable and includes models for core WordPress models such as posts, post metas, users, comments and more.
The ORM is based on [Eloquent ORM](https://laravel.com/docs/8.x/eloquent) and uses the WordPress connection (`wpdb` class).

The ORM also offers a system to simply manage database migrations based on [Phinx](https://phinx.org/).

> 💡 To simplify the integration of this library, we recommend using WordPress with one of the following tools: [Bedrock](https://roots.io/bedrock/), [Themosis](https://framework.themosis.com/) or [Wordplate](https://github.com/wordplate/wordplate#readme).

### Features

- ✅ Support core WordPress models: `Comment`, `Option`, `Post`, `TermTaxonomy`, `Term`, `User`, `PostMeta` and `UserMeta`.
- ✅ Support core WordPress post type: `Article`, `Attachment` and `Page`.
- ✅ Based on core WordPress database connection (`wpdb` class)
- ✅ Migration with `Phinx` library.
- ❤️ Easy integration of a custom post type.
- ❤️ Easy model creation for projects with custom tables.

**Not yet developed but planned in a future version:**

- 💡 Create custom comment type
- 💡 Meta casting (e.g. [Attribute Casting](https://laravel.com/docs/10.x/eloquent-mutators#attribute-casting)) 

### Documentation

- [Installation](#installation)
- [Use WordPress core models](doc/wordpress-core-models.md)
- [Create custom model](doc/create-model.md)
- [Filter  data](/doc/documentation.md#filter-data)
- [Migration with Phinx](doc/migration.md)

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

## Introduction

Simply put, wp-orm is a library that makes it easy to manipulate a WordPress database via the Eloquent ORM. The objective of this library is to **simplify the manipulation of the WordPress database** on large projects - you can also use it for small projects.

> Eloquent is an object-relational mapper (ORM) that makes it enjoyable to interact with your database. When using Eloquent, each database table has a corresponding "Model" that is used to interact with that table. In addition to retrieving records from the database table, Eloquent models allow you to insert, update, and delete records from the table as well.

**Here is a list of available features :**

- The `wpdb` connection is used so **no configuration is needed to use**
- Ability to [create models](doc/documentation.md#model) simply
- WordPress works with custom content types, you can simply [use the default types of WordPress](doc/documentation.md#use-wordpress-models) (page, attachment, ...) and create [custom models for your types](doc/documentation.md#custom-post-type-model) or create [custom model](doc/documentation.md)
- Ability to [filter data](doc/documentation.md#filter-data) easily via taps
- All the features available in Eloquent, are usable with this library

> 📘 If you want to know more about how Eloquent works, the easiest way is to [read the documentation](https://laravel.com/docs/10.x/eloquent).

## Contributing

We encourage you to contribute to this repository, so everyone can benefit from new features, bug fixes, and any other improvements. Have a look at our [contributing guidelines](CONTRIBUTING.md) to find out how to raise a pull request.