# Documentation

## Filter data

You can filter data in several ways:

- With `findOneBy` functions in place on some models
- With taps
- With Eloquent query builder. If your model has metas, you can use custom filter methods.

### With findOneBy

By default, Eloquent does not offer a magic feature [findOneBy*](https://github.com/laravel/ideas/issues/107), however you can use this feature on some models like `User`, `Option` or `Post`.

<details>
    <summary>Available functions</summary>

**User :**

- `User::findOneByEmail()`
- `User::findOneByLogin()`

**Option :**

- `Option::findOneByName()`

**Post :**

- `Post::findOneByName()`
- `Post::findOneByGuid()`

</details>

### With taps

The `tap` method passes the query builder to the given callback, allowing you to "tap" into the query builder at a specific point and do something with the query:

You can easily filter data via the `tap` function :

```php
use Dbout\WpOrm\Taps\Post\IsAuthorTap;
use Dbout\WpOrm\Taps\Post\IsStatusTap;
use Dbout\WpOrm\Enums\PostStatus;
use Dbout\WpOrm\Models\Post;

$posts = Post::query()
    ->tap(new IsAuthorTap(1))
    ->get();
```

This query, returns all user posts with ID 1.

If you want to apply multiple filters, nothing complicated :

```php
use Dbout\WpOrm\Taps\Post\IsAuthorTap;
use Dbout\WpOrm\Taps\Post\IsStatusTap;
use Dbout\WpOrm\Enums\PostStatus;
use Dbout\WpOrm\Models\Post;

$posts = Post::query()
    ->tap(new IsAuthorTap(1))
    ->tap(new IsStatusTap(PostStatus::Publish))
    ->get();
```

You can find all the available filters here: [Available filters](available-filters.md).

### With query builder

The Eloquent `all` method will return all of the results in the model's table. However, since each Eloquent model serves as a [query builder](https://laravel.com/docs/10.x/queries), you may add additional constraints to queries and then invoke the `get` method to retrieve the results:

```php
use Dbout\WpOrm\Models\Post;

$posts = Post::query()
    ->where('ping_status', 'closed')
    ->get();
```

> 📘 More information here: [Eloquent query builder](https://laravel.com/docs/10.x/queries).

#### Model with meta relation

For models that may have metas (e.g. `Post`, `User`, ...), you can filter with `addMetaToFilter`, here is an example that speaks for itself:)

```php
$products = Post::query()
    ->addMetaToFilter('product_type', 'simple')
    ->get();
```

> 📘 You can find all functions usable on models with metas here:

## Events

Eloquent models dispatch several events, allowing you to hook into the following moments in a model's lifecycle: `retrieved`, `creating`, `created`, `updating`, `updated`, `saving`, `saved`, `deleting`, `deleted`, `trashed`, `forceDeleting`, `forceDeleted`, `restoring`, `restored`, and `replicating`.

> 📘 The following few lines explain how to use events with the library, if you want to know more you can look at the documentation: [Eloquent - Events](https://laravel.com/docs/10.x/eloquent#events).

### How to use Eloquent events ?

By default, Eloquent events are not functional in the library since Eloquent is initially used with Laravel which contains all the logic to dispatch the events. However, it is possible to use events via the [illuminate/events](https://packagist.org/packages/illuminate/events) library.

1. First, install `illuminate/events` :

```bash
composer require illuminate/events ^11.0
```

2. Setup the dispatcher in your model with `setEventDispatcher` function :

```php
use Illuminate\Events\Dispatcher;
use Dbout\WpOrm\Orm\AbstractModel;

class User extends AbstractModel 
{
    protected static function boot()
    {
        static::setEventDispatcher(new Dispatcher());
        parent::boot();
    }
}
```

3. Create your events :)

```php
use Illuminate\Events\Dispatcher;
use Dbout\WpOrm\Orm\AbstractModel;

class User extends AbstractModel 
{
    protected static function boot()
    {
        static::setEventDispatcher(new Dispatcher());
        parent::boot();
        
        static::saved(function (User $user) {
            // Add your logic here
        });
    }
}
```

You can now use events, you can use [$dispatchesEvents](https://laravel.com/docs/10.x/eloquent#events) property or [closures](https://laravel.com/docs/10.x/eloquent#events-using-closures).


> Warning, if you use abstract class, you must call `setEventDispatcher` in child class. 

## Meta casting

Meta casting allow you to transform meta values when you retrieve or set them on model instances. For example, you may want to use the Laravel encrypter to encrypt a value while it is stored in the database, and then automatically decrypt the meta when you access it on an Eloquent model. Or, you may want to convert a JSON string that is stored in your database to an array when it is accessed via your Eloquent model. 

**This system works in the same way as the [cast of attributes](https://laravel.com/docs/11.x/eloquent-mutators#attribute-casting).**

The `metaCasts` method should return an array where the key is the name of the meta being cast and the value is the type you wish to cast the column to. The supported cast types are:

- `array`
- `bool`
- `boolean`
- `collection`
- `date`
- `datetime`
- `double`
- `float`
- `immutable_date`
- `int`
- `integer`
- `json`
- `object`
- `string`
- `timestamp`

To demonstrate meta casting, let's cast the `is_admin` meta, which is stored in our database as an integer (0 or 1) to a boolean value:

```php
namespace App\Models;

use Dbout\WpOrm\Models\Post;

class User extends Post
{
    /**
     * @inheritDoc
     */
    protected function metaCasts(): array
    {
        return [
            'is_admin' => 'boolean',
        ];
    }
}
```

After defining the cast, the `is_admin` meta will always be cast to a boolean when you access it, even if the underlying value is stored in the database as an integer:

```php
$user = App\Models\User::find(1);
$isAdmin = $user->getMetaValue('is_admin');
```

> [!WARNING]
> Metas that are `null` will not be cast.

## Facades

[Facades](https://laravel.com/docs/facades) provide a "static" interface to classes that are available in the application's [service container](https://laravel.com/docs/container). Laravel contains several facades including `DB` which is used to easily access the database.

If you want to use the facades, you must initialize the container yourself. The simplest solution for is to create a `mu-plugin`, here’s an example :

```php
use Dbout\WpOrm\Orm\Database;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;

$container = new Container();
$container->instance('db', Database::getInstance());
Facade::setFacadeApplication( $container );
```

You can now use the `DB` facade without any problems :

```php
use \Illuminate\Support\Facades\DB;

$count = DB::raw('count + 1');
```