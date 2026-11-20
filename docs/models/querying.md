# Querying models

Each model is an [Eloquent query builder](https://laravel.com/docs/13.x/queries): all the Eloquent methods (`where`, `whereIn`, `orderBy`, `with`, `chunk`...) are available. On top of that, the library provides a few shortcuts for the WordPress tables.

## Find one model

Eloquent does not provide magic `findOneBy*` methods, but some models have dedicated static methods:

| Method                        | Column       |
|-------------------------------|--------------|
| `Post::findOneByName($name)`  | `post_name`  |
| `Post::findOneByGuid($guid)`  | `guid`       |
| `User::findOneByEmail($email)`| `user_email` |
| `User::findOneByLogin($login)`| `user_login` |
| `Option::findOneByName($name)`| `option_name`|

They return the model, or `null` if nothing matches.

```php
use Dbout\WpOrm\Models\User;

$user = User::findOneByEmail('john@example.com');
```

## Builder methods

The built-in models use a dedicated builder with extra methods:

| Builder          | Method                         | Description |
|------------------|--------------------------------|-------------|
| `PostBuilder`    | `whereTypes(...$types)`        | Filter on one or several post types |
|                  | `whereStatus(...$status)`      | Filter on one or several statuses (`string` or `PostStatus`) |
|                  | `whereAuthor($author)`         | Filter on the author ID |
|                  | `findAllByType($type)`         | Return all the posts of a post type |
| `UserBuilder`    | `whereEmail($email)`           | Filter on the email |
|                  | `whereEmails(...$emails)`      | Filter on several emails |
|                  | `whereLogin($login)`           | Filter on the login |
|                  | `whereLogins(...$logins)`      | Filter on several logins |
| `CommentBuilder` | `whereTypes(...$types)`        | Filter on one or several comment types |
|                  | `findAllByType($type)`         | Return all the comments of a type |
| `OptionBuilder`  | `whereName($name)`             | Filter on the option name |
| `TermBuilder`    | `findAllByTaxonomy($taxonomy)` | Return all the terms of a taxonomy |

The methods with several values accept a list of arguments or an array:

```php
use Dbout\WpOrm\Enums\PostStatus;
use Dbout\WpOrm\Models\Post;

$posts = Post::query()
    ->whereTypes('post', 'page')
    ->whereStatus([PostStatus::Publish, PostStatus::Private])
    ->get();
```

The builders of the models with metas (`PostBuilder`, `UserBuilder`) also have meta filters, see [Metas](metas.md#filter-by-meta).

## Taps

A tap is a reusable filter applied with the Eloquent [`tap`](https://laravel.com/docs/13.x/queries#conditional-clauses) method:

```php
use Dbout\WpOrm\Enums\PostStatus;
use Dbout\WpOrm\Models\Post;
use Dbout\WpOrm\Taps\Post\IsAuthorTap;
use Dbout\WpOrm\Taps\Post\IsStatusTap;

$posts = Post::query()
    ->tap(new IsAuthorTap(1))
    ->tap(new IsStatusTap(PostStatus::Publish))
    ->get();
```

| Model        | Tap                                          | Argument |
|--------------|----------------------------------------------|----------|
| `Post`       | `Dbout\WpOrm\Taps\Post\IsAuthorTap`          | `int` or `User` |
|              | `Dbout\WpOrm\Taps\Post\IsStatusTap`          | `string` or `PostStatus` |
|              | `Dbout\WpOrm\Taps\Post\IsPostTypeTap`        | `string` |
|              | `Dbout\WpOrm\Taps\Post\IsPingStatusTap`      | `string` or `PingStatus` |
| `Attachment` | `Dbout\WpOrm\Taps\Attachment\IsMimeTypeTap`  | `string` (e.g. `image/png`) |
| `Comment`    | `Dbout\WpOrm\Taps\Comment\IsApprovedTap`     | `bool`, `true` by default |
|              | `Dbout\WpOrm\Taps\Comment\IsCommentTypeTap`  | `string` |
|              | `Dbout\WpOrm\Taps\Comment\IsUserTap`         | `int` or `User` |
| `Option`     | `Dbout\WpOrm\Taps\Option\IsAutoloadTap`      | `bool` or `YesNo`, `YesNo::Yes` by default |

A tap is a simple invokable class, you can write your own:

```php
use Dbout\WpOrm\Builders\PostBuilder;
use Dbout\WpOrm\Models\Post;

readonly class IsStickyTap
{
    public function __invoke(PostBuilder $builder): void
    {
        $builder->whereIn(Post::POST_ID, get_option('sticky_posts', []));
    }
}
```

## Query builder

For everything else, use the Eloquent query builder:

```php
use Dbout\WpOrm\Models\Post;

$posts = Post::query()
    ->where(Post::PING_STATUS, 'closed')
    ->whereYear(Post::DATE, 2026)
    ->with('author')
    ->get();
```

See [the Eloquent documentation](https://laravel.com/docs/13.x/eloquent#retrieving-models) to learn more.

> [!NOTE]
> The built-in builders select `table.*` by default, so that the joins (e.g. the [meta filters](metas.md#filter-by-meta)) do not override the columns of the model. Use `addSelect()` to add columns.
