<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpOrm\Models\Multisite;

use Carbon\Carbon;
use Dbout\WpOrm\Orm\AbstractModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method string getEmail()
 * @method RegistrationLog setEmail(string $email)
 * @method string getIP()
 * @method RegistrationLog setIP(string $ip)
 * @method int getBlogId()
 * @method RegistrationLog setBlogId(int $blogId)
 * @method Carbon getDateRegistered()
 * @method RegistrationLog setDateRegistered($dateRegistered)
 *
 * @property-read Blog|null $blog
 */
class RegistrationLog extends AbstractModel
{
    public const CREATED_AT = self::DATE_REGISTERED;
    public const UPDATED_AT = null;

    final public const ID = 'ID';
    final public const EMAIL = 'email';
    final public const IP = 'IP';
    final public const BLOG_ID = 'blog_id';
    final public const DATE_REGISTERED = 'date_registered';

    protected bool $useBasePrefix = true;

    protected $table = 'registration_log';

    protected $primaryKey = self::ID;

    protected $casts = [
        self::BLOG_ID => 'int',
        self::DATE_REGISTERED => 'datetime',
    ];

    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class, self::BLOG_ID);
    }

    /**
     * @see getIP()
     */
    public function getIpAttribute(): ?string
    {
        return $this->getAttributes()[self::IP] ?? null;
    }

    /**
     * @see setIP()
     */
    public function setIpAttribute(mixed $ip): self
    {
        $this->attributes[self::IP] = $ip;
        return $this;
    }
}
