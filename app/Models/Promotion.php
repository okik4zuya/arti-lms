<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A named, time-limited price (an event promo). It is never presented as the launch price: it has its own
 * name and a real end date. A quota is optional; places are handed out through `orders.promo_slot`.
 */
class Promotion extends Model
{
    use HasVersion7Uuids;

    protected $fillable = ['name', 'label', 'price', 'starts_at', 'ends_at', 'quota', 'active'];

    protected static function booted(): void
    {
        static::created(fn (Promotion $p) => SettingChange::record("promotion:{$p->name}", null, $p->only(['label', 'price', 'starts_at', 'ends_at', 'quota', 'active'])));
        static::updated(function (Promotion $p) {
            $fields = array_intersect_key($p->getChanges(), array_flip(['label', 'price', 'starts_at', 'ends_at', 'quota', 'active']));
            if ($fields !== []) {
                SettingChange::record("promotion:{$p->name}", array_intersect_key($p->getOriginal(), $fields), $fields);
            }
        });
        static::deleted(fn (Promotion $p) => SettingChange::record("promotion:{$p->name}", 'exists', 'deleted'));
    }

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'active' => 'boolean', 'price' => 'integer', 'quota' => 'integer'];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** Switched on and inside its dates (the quota is checked separately, it needs the order table). */
    public function scopeRunning(Builder $query): Builder
    {
        return $query->where('active', true)->where('starts_at', '<=', now())->where('ends_at', '>', now());
    }
}
