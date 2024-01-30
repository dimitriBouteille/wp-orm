# Filter data

You can filter data in several ways:

- With predefined `findOneBy` functions in place on some models
- With predefined taps
- With Eloquent query builder. If your model has metas, you can use custom filter methods.

## With findOneBy

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