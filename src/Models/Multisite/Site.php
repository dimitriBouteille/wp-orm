<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models\Multisite;

use Dbout\WpOrm\Concerns\HasMetas;
use Dbout\WpOrm\MetaMappingConfig;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method string getDomain()
 * @method Site setDomain(string $domain)
 * @method string getPath()
 * @method Site setPath(string $path)
 *
 * @property-read Collection<SiteMeta> $metas
 * @property-read Collection<Blog> $blogs
 */
class Site extends AbstractModel
{
    use HasMetas;

    public const CREATED_AT = null;
    public const UPDATED_AT = null;

    final public const ID = 'id';
    final public const DOMAIN = 'domain';
    final public const PATH = 'path';

    protected bool $useBasePrefix = true;

    protected $table = 'site';

    protected $primaryKey = self::ID;

    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class, Blog::SITE_ID);
    }

    public function getMetaConfigMapping(): MetaMappingConfig
    {
        return new MetaMappingConfig(SiteMeta::class, SiteMeta::SITE_ID);
    }
}
