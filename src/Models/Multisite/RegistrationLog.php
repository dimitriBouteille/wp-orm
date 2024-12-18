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
 * @property-read int $ID
 * @property string $email
 * @property string $IP
 * @property int $blog_id
 * @property Carbon $date_registered
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
}
