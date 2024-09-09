# Events

Eloquent models dispatch several events, allowing you to hook into the following moments in a model's lifecycle: `retrieved`, `creating`, `created`, `updating`, `updated`, `saving`, `saved`, `deleting`, `deleted`, `trashed`, `forceDeleting`, `forceDeleted`, `restoring`, `restored`, and `replicating`.

> 📘 The following few lines explain how to use events with the library, if you want to know more you can look at the documentation: [Eloquent - Events](https://laravel.com/docs/10.x/eloquent#events).

## How to use Eloquent events ?

By default, Eloquent events are not functional in the library since Eloquent is initially used with Laravel which contains all the logic to dispatch the events. However, it is possible to use events via the [illuminate/events](https://packagist.org/packages/illuminate/events) library.

1. First, install `illuminate/events` :

```bash
composer require illuminate/events ^10.0
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