<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models\Network;

use Carbon\Carbon;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read int $blog_id
 * @property int $site_id
 * @property string $domain
 * @property string $path
 * @property Carbon $registered
 * @property Carbon $last_updated
 * @property bool $public
 * @property bool $archived
 * @property bool $mature
 * @property bool $spam
 * @property bool $deleted
 * @property int $lang_id
 *
 * @property-read Site $site
 * @property-read BlogVersion|null $version
 */
class Blog extends AbstractModel
{
    public const CREATED_AT = self::REGISTERED;
    public const UPDATED_AT = self::LAST_UPDATED;
    final public const BLOG_ID = 'blog_id';
    final public const SITE_ID = 'site_id';
    final public const DOMAIN = 'domain';
    final public const PATH = 'path';
    final public const REGISTERED = 'registered';
    final public const  LAST_UPDATED = 'last_updated';
    final public const PUBLIC = 'public';
    final public const ARCHIVED = 'archived';
    final public const MATURE = 'mature';
    final public const SPAM = 'spam';
    final public const DELETED = 'deleted';
    final public const LANG_ID = 'lang_id';

    protected $primaryKey = self::BLOG_ID;

    protected bool $useBasePrefix = true;

    protected $casts = [
        self::BLOG_ID => 'int',
        self::SITE_ID => 'int',
        self::REGISTERED => 'datetime',
        self::LAST_UPDATED => 'datetime',
        self::PUBLIC => 'bool',
        self::ARCHIVED => 'bool',
        self::MATURE => 'bool',
        self::SPAM => 'bool',
        self::DELETED => 'bool',
        self::LANG_ID => 'int',
    ];

    protected $table = 'blogs';

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, self::SITE_ID);
    }

    public function version(): HasOne
    {
        return $this->hasOne(BlogVersion::class, BlogVersion::BLOG_ID);
    }
}
