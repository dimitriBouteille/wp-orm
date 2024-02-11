# Filter data

You can filter data in several ways:

- With `findOneBy` functions in place on some models
- With taps
- With Eloquent query builder. If your model has metas, you can use custom filter methods.

## With findOneBy

By default, Eloquent does not offer a magic feature [findOneBy*](https://github.com/laravel/ideas/issues/107), however you can use this feature on some models :

**User :**

- `User::findOneByEmail()`
- `User::findOneByLogin()`

**Option :**

- `Option::findOneByName()`

**Post :**

- `Post::findOneByName()`
- `Post::findOneByGuid()`

## With taps

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

## With query builder

### Generic

The Eloquent `all` method will return all of the results in the model's table. However, since each Eloquent model serves as a [query builder](https://laravel.com/docs/10.x/queries), you may add additional constraints to queries and then invoke the `get` method to retrieve the results:

```php
use Dbout\WpOrm\Models\Post;

$posts = Post::query()
    ->where('ping_status', 'closed')
    ->get();
```

> 📘 More information here: [Eloquent query builder](https://laravel.com/docs/10.x/queries).

### Model with meta relation

For models that may have metas (e.g. `Post`, `User`, ...), you can filter with `addMetaToFilter`, here is an example that speaks for itself:)

```php
$products = Post::query()
    ->addMetaToFilter('product_type', 'simple')
    ->get();
```

> 📘 You can find all functions usable on models with metas here: 