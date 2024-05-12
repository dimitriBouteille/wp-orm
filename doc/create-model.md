# Create Model

You can create two model types:

- A model that corresponds to a custom table (e.g. `User`, `Option`, ...)
- A model that corresponds to a Custom Post Type (e.g `Page`, `Attachment`, `Article`, ...)

## Generic Model

Creating a model is very simple, just create a class that extends from `Dbout\WpOrm\Orm\AbstractModel`.

```php
use Dbout\WpOrm\Orm\AbstractModel;

class MyModel extends AbstractModel 
{

}
```

Note that we did not tell Eloquent which table to use for our `MyModel` model. The "snake case", plural name of the class will be used as the table name unless another name is explicitly specified. So, in this case, Eloquent will assume the `MyModel` model stores records in the myModels table. You may specify a custom table by defining a table property on your model:

```php
use Dbout\WpOrm\Orm\AbstractModel;

class MyModel extends AbstractModel
{

    protected $table = 'my_table';
}
```

**Note:** Eloquent will also assume that each table has a primary key column named id. You may define a primaryKey property to override this convention. Likewise, you may define a connection property to override the name of the database connection that should be used when utilizing the model.

Once a model is defined, you are ready to start retrieving and creating records in your table. Note that you will need to place `updated_at` and `created_at` columns on your table by default. If you do not wish to have these columns automatically maintained, set the `$timestamps` property on your model to false.

> 📘 If you want to know more about creating a model you can look the [Eloquent documentation](https://laravel.com/docs/10.x/eloquent#eloquent-model-conventions).

### Add meta relation

If your model have metas, you can easily link metas to your model and use custom functions (e.g. `getMeta`, `getMetaValue`, ...). You can look `Dbout\WpOrm\Models\Post` to understand how it works.

## Custom Post Type Model

All Custom Post Type (CPT) models extend `Dbout\WpOrm\Models\CustomPost`.

```php
use Dbout\WpOrm\Models\CustomPost;

class MyCustomType extends CustomPost
{
    /**
     * @inheritDoc
     */
    protected string $_type = 'my_customm_type';
}
```

When retrieving a model `MyCustomType`, the `posts.post_type = my_customm_type` filter will be automatically added to the query.

When creating the model, the `post_type` property is automatically filled in with the value `my_customm_type`.

**Note:** You cannot use `setPostType` function on CPT models.
