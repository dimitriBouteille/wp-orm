# Wordpress ORM with Eloquent

![GitHub Release](https://img.shields.io/github/v/release/dimitriBouteille/wp-orm) ![Packagist Downloads](https://img.shields.io/packagist/dt/dbout/wp-orm)

WordPress ORM wih Eloquent is a small library that adds a basic ORM into WordPress, which is easily extendable and includes models for core WordPress models such as posts, post metas, users, comments and more.
The ORM is based on [Eloquent ORM](https://laravel.com/docs/8.x/eloquent) and uses the Wordpress connection (`wpdb` class).

The ORM also offers a system to simply manage database migrations based on [Phinx](https://phinx.org/).

💡 To simplify the integration of this library, we recommend using Wordpress with one of the following tools: [Bedrock](https://roots.io/bedrock/), [Themosis](https://framework.themosis.com/) or [Wordplate](https://github.com/wordplate/wordplate#readme).

### Features

- ✅ Support core WordPress models: `Comment`, `Option`, `Post`, `TermTaxonomy`, `Term`, `User`, `PostMeta` and `UserMeta`.
- ✅ Support core WordPress post type: `Article`, `Attachment` and `Page`.
- ✅ Based on core Wordpress database connection (`wpdb` class)
- ✅ Migration with `Phinx` library.
- ❤️ Easy integration of a custom post type.
- ❤️ Easy model creation for projects with custom tables.

**Not yet developed but planned in a future version:**

- 💡 Create custom comment type
- 💡 Meta casting (ie [Attribute Casting](https://laravel.com/docs/10.x/eloquent-mutators#attribute-casting)) 

### Documentation

- [Installation](#installation)
- [Introduction]()
- [Use the Wordpress models]()
- [Create custom Model]()
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

## Contributing

We encourage you to contribute to this repository, so everyone can benefit from new features, bug fixes, and any other improvements. Have a look at our [contributing guidelines](CONTRIBUTING.md) to find out how to raise a pull request.