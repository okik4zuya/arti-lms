<?php

namespace App\Models;

use App\Mail\OrderAttentionMail;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * One gateway order. The status moves forward only through compare-and-set updates (see transition()),
 * so a repeated webhook cannot fulfil an order twice.
 */
class Order extends Model
{
    use HasVersion7Uuids;

    public const PENDING = 'pending';

    public const PAID = 'paid';

    public const FULFILLING = 'fulfilling';

    public const FULFILLED = 'fulfilled';

    public const EXPIRED = 'expired';

    public const FAILED = 'failed';

    public const REFUNDED = 'refunded';

    public const NEEDS_ATTENTION = 'needs_attention';

    protected $fillable = [
        'public_id', 'type', 'name', 'email', 'whatsapp', 'amount', 'price_tier', 'launch_slot', 'promotion_id', 'promo_slot',
        'status', 'gateway', 'gateway_ref', 'method_choice', 'checkout_url', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'paid_at' => 'datetime',
            'fulfilled_at' => 'datetime',
            'email_sent_at' => 'datetime',
            'whatsapp_sent_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->public_id ??= 'arti-'.Str::lower(Str::random(20));
        });
    }

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function licenseIssuance(): BelongsTo
    {
        return $this->belongsTo(LicenseIssuance::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(OrderEvent::class)->latest('created_at');
    }

    public function logEvent(string $event, ?string $detail = null, ?string $userId = null): void
    {
        $this->events()->create(['event' => $event, 'detail' => $detail, 'user_id' => $userId, 'created_at' => now()]);
    }

    /**
     * Park the order for an admin: money may already have been taken, so nothing more happens automatically.
     * Returns whether this call did it. The admins get one mail; the note is a code, never a key or token.
     */
    public function flagAttention(string $note, ?string $detail = null): bool
    {
        $flagged = $this->transition([self::PENDING, self::EXPIRED, self::PAID, self::FULFILLING], self::NEEDS_ATTENTION, ['attention_note' => $note]);

        if ($flagged) {
            $this->logEvent($note, $detail);
            $admins = User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->pluck('email')->all();

            if ($admins !== []) {
                Mail::to($admins)->queue(new OrderAttentionMail($this->id, $note));
            }
        }

        return $flagged;
    }

    /**
     * Move to $to only if the order is still in one of $from. Returns whether this call made the change,
     * which is what makes the webhook idempotent: of two concurrent callbacks exactly one gets true.
     *
     * @param  list<string>  $from
     * @param  array<string, mixed>  $extra
     */
    public function transition(array $from, string $to, array $extra = []): bool
    {
        $changed = static::query()
            ->whereKey($this->getKey())
            ->whereIn('status', $from)
            ->update($extra + ['status' => $to, 'updated_at' => now()]);

        if ($changed) {
            $this->refresh();
        }

        return $changed === 1;
    }
}
