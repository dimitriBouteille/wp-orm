# Custom models

You can create three kinds of models:

- [A model for a custom table](#custom-table) (e.g. a `wp_orders` table)
- [A model for a custom post type](#custom-post-type) (like `Page` or `Attachment`)
- [A model for a custom comment type](#custom-comment-type)

## Custom table

Create a class that extends `Dbout\WpOrm\Orm\AbstractModel`:

```php
namespace App\Models;

use Dbout\WpOrm\Orm\AbstractModel;

class Order extends AbstractModel
{
    protected $table = 'orders';
}
```

The table name is written **without the WordPress prefix**, it is added automatically: the model above uses the `wp_orders` table. If you do not define `$table`, Eloquent uses the snake case, plural name of the class (`orders` for `Order`).

The usual [Eloquent conventions](https://laravel.com/docs/13.x/eloquent#eloquent-model-conventions) apply:

- The primary key is `id`, use `$primaryKey` to change it.
- The table must have the `created_at` and `updated_at` columns, set `$timestamps` to `false` if it does not.

```php
class Order extends AbstractModel
{
    protected $table = 'orders';
    protected $primaryKey = 'order_id';
    public $timestamps = false;
}
```

Your model has the same features as the built-in models: magic getters / setters (`getReference()`, `setReference()`), `getId()`, relations to the WordPress models...

```php
use Dbout\WpOrm\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends AbstractModel
{
    protected $table = 'orders';

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id', User::USER_ID);
    }
}
```

> [!TIP]
> Use the [migrations](../database/migrations.md) to create and update your custom tables.

### Add metas

If your table has a meta table, you can get the [meta features](metas.md) (`getMetaValue()`, `addMetaToFilter()`, meta casting...) in four steps.

1. Create the meta model, it extends `Dbout\WpOrm\Models\Meta\AbstractMeta`:

```php
namespace App\Models;

use Dbout\WpOrm\Models\Meta\AbstractMeta;

class OrderMeta extends AbstractMeta
{
    protected $table = 'order_meta';
    protected $primaryKey = 'meta_id';
}
```

The meta table must have the `meta_key` and `meta_value` columns, and a column with the ID of the parent model (`order_id` here).

2. Create the builder, it extends `Dbout\WpOrm\Builders\AbstractWithMetaBuilder`:

```php
namespace App\Builders;

use Dbout\WpOrm\Builders\AbstractWithMetaBuilder;

class OrderBuilder extends AbstractWithMetaBuilder
{
}
```

3. In the model, implement `WithMetaModelInterface`, use the `HasMetas` trait and return the meta configuration:

```php
namespace App\Models;

use App\Builders\OrderBuilder;
use Dbout\WpOrm\Api\WithMetaModelInterface;
use Dbout\WpOrm\Concerns\HasMetas;
use Dbout\WpOrm\MetaMappingConfig;
use Dbout\WpOrm\Orm\AbstractModel;

class Order extends AbstractModel implements WithMetaModelInterface
{
    use HasMetas;

    protected $table = 'orders';

    public function getMetaConfigMapping(): MetaMappingConfig
    {
        return new MetaMappingConfig(OrderMeta::class, 'order_id');
    }

    public function newEloquentBuilder($query): OrderBuilder
    {
        return new OrderBuilder($query);
    }
}
```

4. That's all:

```php
$order = Order::find(1);
$order->setMeta('tracking_number', 'XY123');

Order::query()->addMetaToFilter('tracking_number', 'XY123')->get();
```

## Custom post type

A custom post type model extends `Dbout\WpOrm\Models\CustomPost` and defines the post type in `$_type`:

```php
namespace App\Models;

use Dbout\WpOrm\Models\CustomPost;

class Product extends CustomPost
{
    protected string $_type = 'product';
}
```

- The queries are automatically filtered on the post type: `Product::all()` only returns the `product` posts.
- The `post_type` column is automatically filled when you create a model.
- The model has all the features of `Post`: metas, relations, builder methods...

```php
$product = new Product();
$product->setPostTitle('T-shirt');
$product->save(); // post_type = 'product'
```

> [!WARNING]
> The post type of a custom post type model cannot be changed: `setPostType()` throws a `Dbout\WpOrm\Exceptions\CannotOverrideCustomTypeException`.

To query without the post type filter, remove the global scope:

```php
use Dbout\WpOrm\Scopes\CustomModelTypeScope;

Product::withoutGlobalScope(CustomModelTypeScope::class)->get();
```

## Custom comment type

A custom comment type model works the same way: it extends `Dbout\WpOrm\Models\CustomComment` and defines the comment type in `$_type`:

```php
namespace App\Models;

use Dbout\WpOrm\Models\CustomComment;

class Review extends CustomComment
{
    protected string $_type = 'review';
}
```

The queries are filtered on the `comment_type` column, the column is filled when you create a model, and `setCommentType()` throws a `CannotOverrideCustomTypeException`.

## Extend a built-in model

You can also extend a built-in model to add your own methods, relations or [meta casts](metas.md#meta-casting):

```php
namespace App\Models;

use Dbout\WpOrm\Models\User as BaseUser;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends BaseUser
{
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id', self::USER_ID);
    }

    protected function metaCasts(): array
    {
        return [
            'is_premium' => 'boolean',
        ];
    }
}
```
