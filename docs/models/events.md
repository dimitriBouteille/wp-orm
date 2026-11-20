# Events

Eloquent models dispatch [events](https://laravel.com/docs/13.x/eloquent#events) at each step of their lifecycle: `retrieved`, `creating`, `created`, `updating`, `updated`, `saving`, `saved`, `deleting`, `deleted`, `trashed`, `forceDeleting`, `forceDeleted`, `restoring`, `restored` and `replicating`.

## Enable the events

In a Laravel application, the event dispatcher is configured by the framework. With this library, you must configure it yourself, once, before using the models. The simplest is a [must-use plugin](https://developer.wordpress.org/advanced-administration/plugins/mu-plugins/):

```php
// wp-content/mu-plugins/wp-orm.php
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Events\Dispatcher;

AbstractModel::setEventDispatcher(new Dispatcher());
```

`illuminate/events` is already installed with the library, there is nothing else to install.

> [!WARNING]
> The dispatcher is shared by all the models: configure it **once**, before the first model is used. Do not call `setEventDispatcher(new Dispatcher())` in the `boot()` method of each model: each call replaces the dispatcher, and the listeners registered by the models booted before are lost.

> [!NOTE]
> The dispatcher is also needed to save the [metas of a new model](metas.md#metas-of-a-new-model).

## Listen to the events

Once the dispatcher is configured, all the Eloquent ways to listen to the events work. For example with closures in the `booted()` method of your model:

```php
namespace App\Models;

use Dbout\WpOrm\Models\CustomPost;

class Product extends CustomPost
{
    protected string $_type = 'product';

    protected static function booted(): void
    {
        parent::booted();

        static::saved(function (Product $product): void {
            clean_post_cache($product->getId());
        });
    }
}
```

> [!WARNING]
> `CustomPost` and `CustomComment` register their post type filter in `booted()`: always call `parent::booted()` when you override it.

Or with the `$dispatchesEvents` property, an [observer](https://laravel.com/docs/13.x/eloquent#observers), or directly with the dispatcher:

```php
use App\Models\Product;

Product::created(function (Product $product): void {
    // ...
});
```

See [the Eloquent documentation](https://laravel.com/docs/13.x/eloquent#events) to learn more.

> [!NOTE]
> The Eloquent events are not the WordPress hooks: saving a `Post` model does not trigger `save_post`, and calling `wp_insert_post()` does not trigger the `saved` event of the model.
