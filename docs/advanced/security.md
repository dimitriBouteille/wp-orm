# Security

## Mass assignment

> [!WARNING]
> **Mass assignment is wide open by default.**
> Every model inherits `protected $guarded = []`, which means **every column is mass-assignable**.

A call like `User::create($_POST)` would let a caller set sensitive columns such as `user_pass` or `user_activation_key`. When you accept user input, always validate it, or define `$fillable` / `$guarded` on your model:

```php
namespace App\Models;

use Dbout\WpOrm\Models\User as BaseUser;

class SafeUser extends BaseUser
{
    protected $fillable = [
        self::LOGIN,
        self::EMAIL,
        self::DISPLAY_NAME,
    ];
}
```

See [Mass assignment](https://laravel.com/docs/13.x/eloquent#mass-assignment) in the Eloquent documentation.

## Passwords

The models do not hash anything: `setUserPass('secret')` saves `secret` in clear text. Use `wp_hash_password()`, or better the WordPress functions (`wp_set_password()`, `wp_insert_user()`) which also clear the caches.

## SQL injection

The values passed to the query builder (`where()`, `insert()`, `update()`...) are escaped with `$wpdb->prepare()`. As with Laravel, the following are **not** escaped and must never contain user input:

- the raw expressions: `DB::raw()`, `selectRaw()`, `whereRaw()`, `orderByRaw()`...
- the column and table names (e.g. `orderBy($_GET['sort'])`): check them against a list of allowed values.

The meta keys passed to `addMetaToFilter()`, `addMetaToSelect()` and `joinToMeta()` are used as SQL aliases: they are validated and only accept letters, digits and underscores.

## Serialized options

The `option_value` column of `Option` is unserialized with `allowed_classes => false`: a serialized object is returned as `__PHP_Incomplete_Class` and never instantiated, so a malicious option value cannot trigger a PHP object injection.
