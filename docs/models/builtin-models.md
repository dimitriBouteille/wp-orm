# Built-in models

The library ships a model for each core WordPress table. The table prefix (`wp_` by default) is added automatically.

| Model                                   | Table                   | Metas | Builder         |
|-----------------------------------------|-------------------------|-------|-----------------|
| `Dbout\WpOrm\Models\Post`               | `wp_posts`              | ✅    | `PostBuilder`   |
| `Dbout\WpOrm\Models\User`               | `wp_users`              | ✅    | `UserBuilder`   |
| `Dbout\WpOrm\Models\Comment`            | `wp_comments`           | ❌    | `CommentBuilder`|
| `Dbout\WpOrm\Models\Option`             | `wp_options`            | ❌    | `OptionBuilder` |
| `Dbout\WpOrm\Models\Term`               | `wp_terms`              | ❌    | `TermBuilder`   |
| `Dbout\WpOrm\Models\TermTaxonomy`       | `wp_term_taxonomy`      | ❌    | -               |
| `Dbout\WpOrm\Models\TermRelationship`   | `wp_term_relationships` | ❌    | -               |
| `Dbout\WpOrm\Models\Meta\PostMeta`      | `wp_postmeta`           | -     | -               |
| `Dbout\WpOrm\Models\Meta\UserMeta`      | `wp_usermeta`           | -     | -               |

The builders are in the `Dbout\WpOrm\Builders` namespace, see [Querying models](querying.md).

## Post types

Some core post types have their own model. They extend `Post` and are automatically filtered on their post type:

| Model                              | Post type    |
|------------------------------------|--------------|
| `Dbout\WpOrm\Models\Article`       | `post`       |
| `Dbout\WpOrm\Models\Page`          | `page`       |
| `Dbout\WpOrm\Models\Attachment`    | `attachment` |

```php
use Dbout\WpOrm\Models\Page;

// Only returns the pages
$pages = Page::all();
```

`Post` is not filtered: it returns every post type. You can create a model for your own post types, see [Custom models](custom-models.md#custom-post-type).

## Columns

Each model exposes its columns as constants, use them instead of hard-coded strings:

```php
use Dbout\WpOrm\Models\Post;

Post::query()
    ->where(Post::STATUS, 'publish')
    ->orderBy(Post::DATE, 'desc')
    ->get();
```

| Model     | Constants |
|-----------|-----------|
| `Post`    | `POST_ID`, `AUTHOR`, `DATE`, `DATE_GMT`, `CONTENT`, `TITLE`, `EXCERPT`, `COMMENT_STATUS`, `STATUS`, `PING_STATUS`, `PASSWORD`, `POST_NAME`, `TO_PING`, `PINGED`, `MODIFIED`, `MODIFIED_GMT`, `CONTENT_FILTERED`, `PARENT`, `GUID`, `MENU_ORDER`, `TYPE`, `MIME_TYPE`, `COMMENT_COUNT` |
| `User`    | `USER_ID`, `LOGIN`, `PASSWORD`, `NICE_NAME`, `EMAIL`, `URL`, `REGISTERED`, `ACTIVATION_KEY`, `DISPLAY_NAME`, `STATUS` |
| `Comment` | `COMMENT_ID`, `POST_ID`, `AUTHOR`, `AUTHOR_EMAIL`, `AUTHOR_URL`, `AUTHOR_IP`, `DATE`, `DATE_GMT`, `CONTENT`, `KARMA`, `APPROVED`, `AGENT`, `TYPE`, `PARENT`, `USER_ID` |
| `Option`  | `OPTION_ID`, `NAME`, `VALUE`, `AUTOLOAD` |
| `Term`    | `TERM_ID`, `NAME`, `SLUG`, `TERM_GROUP` |
| `TermTaxonomy` | `TERM_TAXONOMY_ID`, `TERM_ID`, `TAXONOMY`, `DESCRIPTION`, `PARENT`, `COUNT` |
| `TermRelationship` | `OBJECT_ID`, `TERM_TAXONOMY_ID`, `TERM_ORDER` |

## Getters and setters

Every column can be read and written with a magic getter / setter. The method name is converted to snake case to find the column:

```php
$post->setPostTitle('Hello world');   // post_title
$post->getPostTitle();                // post_title
$post->getCommentCount();             // comment_count
$user->getUserEmail();                // user_email
```

`getId()` returns the value of the primary key, whatever its column name (`ID`, `comment_ID`, `option_id`...).

The regular Eloquent attribute syntax works too: `$post->post_title`, `$post->post_title = 'Hello world'`.

## Relations

| Model          | Relation       | Type                       |
|----------------|----------------|----------------------------|
| `Post`         | `author`       | `BelongsTo` `User`         |
|                | `comments`     | `HasMany` `Comment`        |
|                | `parent`       | `BelongsTo` `Post`         |
|                | `metas`        | `HasMany` `PostMeta`       |
| `User`         | `posts`        | `HasMany` `Post`           |
|                | `comments`     | `HasMany` `Comment`        |
|                | `metas`        | `HasMany` `UserMeta`       |
| `Comment`      | `post`         | `BelongsTo` `Post`         |
|                | `user`         | `BelongsTo` `User`         |
|                | `parent`       | `BelongsTo` `Comment`      |
| `Term`         | `termTaxonomy` | `HasOne` `TermTaxonomy`    |
| `PostMeta`     | `post`         | `BelongsTo` `Post`         |
| `UserMeta`     | `user`         | `BelongsTo` `User`         |

```php
use Dbout\WpOrm\Models\User;

// Eager load the posts and their comments
$users = User::with('posts.comments')->get();
```

## Casts

The integer and date columns are cast to native types: `$post->getPostDate()` returns a `Carbon` instance, `$post->getPostAuthor()` an `int`.

The `option_value` column of `Option` is cast with `Dbout\WpOrm\Casts\WpSerializedCast`: values serialized by WordPress are unserialized when read, arrays and objects are serialized when written.

```php
use Dbout\WpOrm\Models\Option;

$option = Option::findOneByName('active_plugins');
$plugins = $option->getOptionValue(); // array
```

> [!NOTE]
> For security reasons, serialized objects are never instantiated: they are returned as `__PHP_Incomplete_Class`.

## Timestamps

The Eloquent timestamps are mapped to the WordPress columns:

| Model     | `CREATED_AT`      | `UPDATED_AT`    |
|-----------|-------------------|-----------------|
| `Post`    | `post_date`       | `post_modified` |
| `User`    | `user_registered` | -               |
| `Comment` | `comment_date`    | -               |

The other models have no timestamps.

> [!WARNING]
> Only the local date columns are filled automatically, the GMT columns (`post_date_gmt`, `post_modified_gmt`, `comment_date_gmt`) must be set by hand.

## Enums

The library provides a few enums for the WordPress values:

| Enum                              | Cases |
|-----------------------------------|-------|
| `Dbout\WpOrm\Enums\PostStatus`    | `Publish`, `Future`, `Draft`, `Pending`, `Private`, `Trash`, `AutoDraft`, `Inherit` |
| `Dbout\WpOrm\Enums\PingStatus`    | `Open`, `Closed` |
| `Dbout\WpOrm\Enums\YesNo`         | `Yes`, `No` |
