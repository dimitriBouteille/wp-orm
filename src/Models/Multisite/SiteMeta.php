<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models\Multisite;

use Dbout\WpOrm\Models\Meta\AbstractMeta;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read int $meta_id
 * @property int $site_id
 * @property string|null $meta_key
 * @property mixed|null $meta_value
 *
 * @property-read Site $site
 */
class SiteMeta extends AbstractMeta
{
    final public const META_ID = 'meta_id';
    final public const SITE_ID = 'site_id';

    protected bool $useBasePrefix = true;

    protected $table = 'sitemeta';

    protected $primaryKey = self::META_ID;

    public function site(): HasOne
    {
        return $this->hasOne(Site::class, Site::ID, self::SITE_ID);
    }
}
