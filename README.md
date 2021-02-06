# Wordpress ORM with Eloquent

WordPress ORM wih Eloquent is a small library that adds a basic ORM into WordPress, which is easily extendable and includes models for core WordPress models such as posts, post metas, users, comments and more.
The ORM is based on [Eloquent ORM](https://laravel.com/docs/8.x/eloquent) and uses the Wordpress connection (`wpdb` class).

The ORM also offers a system to simply manage database migrations.

### Installation

Install with composer, in the root of the Wordpress project run:

```bash
composer require dbout/wp-orm
```
