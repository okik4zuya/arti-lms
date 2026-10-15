<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Who did what with a license record (key revealed, message sent, ...). Never stores the key. */
class LicenseIssuanceEvent extends Model
{
    use HasVersion7Uuids;

    public const UPDATED_AT = null;

    protected $fillable = ['license_issuance_id', 'user_id', 'event', 'detail'];

    public function licenseIssuance(): BelongsTo
    {
        return $this->belongsTo(LicenseIssuance::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
