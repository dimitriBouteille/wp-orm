# Wordpress ORM with Eloquent

WordPress ORM wih Eloquent is a small library that adds a basic ORM into WordPress, which is easily extendable and includes models for core WordPress models such as posts, post metas, users, comments and more.
The ORM is based on [Eloquent ORM](https://laravel.com/docs/8.x/eloquent) and uses the Wordpress connection (`wpdb` class).

The ORM also offers a system to simply manage database migrations based on [Phinx](https://phinx.org/).

Requirements
--------

The server requirements are basically the same as for [WordPress](https://wordpress.org/about/requirements/) with the addition of a few ones :

- PHP >= 7.4
- [Composer](https://getcomposer.org/) ❤️

> To simplify the integration of this library, we recommend using Wordpress with one of the following tools: [Bedrock](https://roots.io/bedrock/), [Themosis](https://framework.themosis.com/) or [Wordplate](https://github.com/wordplate/wordplate#readme).

Installation
--------


Install with composer, in the root of the Wordpress project run:

```bash
composer require dbout/wp-orm
```

Basic usage with core Wordpress models 
--------

### Page model

#### Queries

```php
# Get all pages
$pages = \Dbout\WpOrm\Models\Page::all();

# Get one page by ID
$page = \Dbout\WpOrm\Models\Page::find(15);

# Get all pages by author
$pages = \Dbout\WpOrm\Models\Page::query()
    ->whereAuthor(1)
    ->get();

# Get all publish pages
$pages = \Dbout\WpOrm\Models\Page::query()
    ->whereStatus('publish')
    ->get();
```


### Migration

#### Create new migration : 

```bash
php vendor/bin/phinx create -c config-phinx.php
```

#### Run migration :

```bash
php vendor/bin/phinx migrate -c config-phinx.php
```