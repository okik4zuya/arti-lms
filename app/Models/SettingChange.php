<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One audited change to a price, a quota or a promotion. */
class SettingChange extends Model
{
    use HasVersion7Uuids;

    public $timestamps = false;

    protected $fillable = ['key', 'old_value', 'new_value', 'user_id', 'created_at'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(string $key, mixed $old, mixed $new): void
    {
        static::create([
            'key' => $key,
            'old_value' => $old === null ? null : (is_scalar($old) ? (string) $old : json_encode($old)),
            'new_value' => $new === null ? null : (is_scalar($new) ? (string) $new : json_encode($new)),
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);
    }
}
