# Metas

The models with metas (`Post`, `User` and their children) have methods to read, write and filter their metas, and support meta casting.

## Read and write metas

| Method                         | Description |
|--------------------------------|-------------|
| `getMeta($key)`                | Return the meta model (`PostMeta`, `UserMeta`) or `null` |
| `getMetaValue($key)`           | Return the meta value (cast if a [meta cast](#meta-casting) is defined) or `null` |
| `hasMeta($key)`                | Check if the meta exists |
| `setMeta($key, $value)`        | Create or update the meta |
| `deleteMeta($key)`             | Delete the meta |
| `metas()`                      | The `HasMany` relation of all the metas |

```php
use Dbout\WpOrm\Models\Post;

$post = Post::find(42);

$post->setMeta('color', 'blue');
$post->getMetaValue('color');   // 'blue'
$post->hasMeta('color');        // true
$post->deleteMeta('color');

// All the metas
foreach ($post->metas as $meta) {
    echo $meta->getMetaKey() . ': ' . $meta->getValue();
}
```

On a saved model, `setMeta()` and `deleteMeta()` write in the database immediately, there is no need to call `save()`.

### Metas of a new model

On a model that is not saved yet, the metas are kept in memory and written when the model is saved:

```php
$post = new Post();
$post->setPostTitle('Hello world');
$post->setMeta('color', 'blue');
$post->save(); // Saves the post, then the "color" meta
```

> [!WARNING]
> This feature relies on the Eloquent `saved` event: it only works when an event dispatcher is configured, see [Events](events.md). Without dispatcher, the metas of a new model are silently lost. If you do not use events, save the model first, then call `setMeta()`.

### Serialized values

The metas are read and written as raw values. A value serialized by WordPress (e.g. an array saved with `update_post_meta()`) is returned as the serialized string. Use `maybe_unserialize()` or the WordPress meta functions to read it.

## Filter by meta

The builders of the models with metas have methods to filter and select metas. Each method adds a join on the meta table, aliased with the meta key.

| Method                                         | Description |
|------------------------------------------------|-------------|
| `addMetaToFilter($key, $value, $operator = '=')` | Filter on the meta value |
| `addMetaToSelect($key, $alias = null)`         | Add the meta value to the selected columns, as `{key}_value` by default |
| `addMetasToSelect(array $keys)`                | Add several metas to the selected columns, use a string key to define the alias |
| `joinToMeta($key, $joinType = 'inner')`        | Join the meta table (`inner`, `left` or `right`) |

```php
use Dbout\WpOrm\Models\Post;

$products = Post::query()
    ->whereTypes('product')
    ->addMetaToFilter('price', 100, '>=')
    ->addMetasToSelect(['sku', 'stock' => '_stock'])
    ->get();

$products->first()->sku_value;
$products->first()->stock;
```

> [!NOTE]
> `addMetaToFilter()` and `addMetaToSelect()` use an inner join: only the models that have the meta are returned. To select a meta while keeping the models without it, call `joinToMeta($key, 'left')` before `addMetaToSelect($key)`.

> [!WARNING]
> As the meta key is used as an SQL alias, it may only contain letters, digits and underscores, and must start with a letter or an underscore (e.g. `_stock`, `price`). Any other key (e.g. `birthday-date`) throws a `Dbout\WpOrm\Exceptions\WpOrmException`. Use the `metas` relation to filter on such keys:
>
> ```php
> use Dbout\WpOrm\Models\Meta\PostMeta;
>
> Post::query()->whereHas('metas', function ($query) {
>     $query->where(PostMeta::META_KEY, 'birthday-date')
>         ->where(PostMeta::META_VALUE, '1950-10-31');
> })->get();
> ```

## Meta casting

Meta casting transforms the meta values when you read them with `getMetaValue()`, like the Eloquent [attribute casting](https://laravel.com/docs/13.x/eloquent-mutators#attribute-casting) does for the columns.

Define the casts with the `metaCasts()` method, where the key is the meta key and the value the cast type:

```php
namespace App\Models;

use Dbout\WpOrm\Models\User as BaseUser;

class User extends BaseUser
{
    protected function metaCasts(): array
    {
        return [
            'is_premium' => 'boolean',
            'birthday' => 'date',
            'preferences' => 'array',
        ];
    }
}
```

```php
$user = App\Models\User::find(1);
$user->getMetaValue('is_premium'); // true instead of '1'
$user->getMetaValue('birthday');   // Carbon instance
```

The `$metaCasts` property works too, both are merged:

```php
class User extends BaseUser
{
    protected array $metaCasts = [
        'is_premium' => 'boolean',
    ];
}
```

### Cast types

| Type                                        | Result |
|---------------------------------------------|--------|
| `int`, `integer`                            | `int` |
| `float`, `double`                           | `float` |
| `decimal:<precision>` (e.g. `decimal:2`)    | `string` with the given precision |
| `string`                                    | `string` |
| `bool`, `boolean`                           | `bool` |
| `array`, `json`                             | `array`, decoded from JSON |
| `object`                                    | `stdClass`, decoded from JSON |
| `collection`                                | `Illuminate\Support\Collection`, decoded from JSON |
| `date`, `datetime`, `datetime:<format>`     | `Carbon` |
| `immutable_date`, `immutable_datetime`, `immutable_datetime:<format>` | `CarbonImmutable` |
| `timestamp`                                 | `int` (Unix timestamp) |
| An enum class (e.g. `Status::class`)        | The enum case |

> [!NOTE]
> - `null` values are never cast.
> - The casts only apply when reading with `getMetaValue()`: the value written with `setMeta()` is saved as is.
> - `array`, `json`, `object` and `collection` decode JSON, not the PHP serialization used by WordPress.
