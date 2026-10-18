<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Commercial record of one license. The Worker owns status, dates and activations; this row mirrors
 * them for display (the Worker wins on any difference) and keeps the key, encrypted, so it can be resent.
 */
class LicenseIssuance extends Model
{
    use HasVersion7Uuids;

    public const PENDING = 'pending';

    public const ACTIVE = 'active';

    public const REVOKED = 'revoked';

    public const EXPIRED = 'expired';

    public const MISSING = 'missing_in_worker';

    protected $fillable = [
        'order_ref', 'source', 'email', 'name', 'tier', 'seats', 'months', 'amount_idr',
        'buyer_whatsapp', 'user_id', 'issued_by', 'notes', 'payment_ref', 'payment_proof_path',
    ];

    /** The key must never leave the server through toArray()/JSON. */
    protected $hidden = ['key'];

    protected function casts(): array
    {
        return [
            'key' => 'encrypted',
            'key_stale' => 'boolean',
            'key_checked_at' => 'datetime',
            'expires_at' => 'datetime',
            'updates_until' => 'datetime',
            'wa_sent_at' => 'datetime',
            'payment_verified_at' => 'datetime',
            'last_synced_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_verified_by');
    }

    public function events(): HasMany
    {
        return $this->hasMany(LicenseIssuanceEvent::class);
    }

    /** Licenses that really exist at the Worker (everything except unfinished attempts). */
    public function scopeSold(Builder $query): Builder
    {
        return $query->where('status', '!=', self::PENDING);
    }

    /**
     * The "Perlu ditinjau" queue: manual licenses whose payment an admin has not verified, plus rows with a
     * review note (free license, compensation or unpaid extension) until an admin marks them reviewed.
     */
    public function scopeNeedsReview(Builder $query): Builder
    {
        return $query->where('status', '!=', self::PENDING)->where(function (Builder $q) {
            $q->where(fn (Builder $unpaid) => $unpaid->where('source', 'manual')->whereNull('payment_verified_at'))
                ->orWhereNotNull('review_note');
        });
    }

    /** verified | waiting (a reference or proof exists, no admin check yet) | missing | none (pending row). */
    public function paymentState(): string
    {
        return match (true) {
            $this->payment_verified_at !== null => 'verified',
            $this->isPending() => 'none',
            filled($this->payment_ref) || filled($this->payment_proof_path) => 'waiting',
            default => 'missing',
        };
    }

    /** "berlaku selamanya" for a lifetime license (issued for about 83 years), otherwise "berlaku sampai <date>". */
    public function validityLabel(): string
    {
        return match (true) {
            $this->expires_at === null => 'masa berlaku belum diketahui',
            $this->expires_at->greaterThan(now()->addYears(50)) => 'berlaku selamanya',
            default => 'berlaku sampai '.$this->expires_at->locale('id')->translatedFormat('j F Y'),
        };
    }

    public function isPending(): bool
    {
        return $this->status === self::PENDING;
    }

    /** True when the stored key can be shown or sent. */
    public function hasUsableKey(): bool
    {
        return filled($this->key) && ! $this->key_stale;
    }

    /**
     * Copy the Worker's view of the license (the `license` and `activations` parts of admin/show) onto
     * this row. The Worker wins on any difference. Does not save.
     *
     * @param  array<string,mixed>  $license
     * @param  array<int,array<string,mixed>>|null  $activations
     */
    public function applyWorkerState(array $license, ?array $activations = null): static
    {
        $expires = isset($license['expires']) ? Carbon::parse($license['expires']) : $this->expires_at;

        $this->tier = $license['tier'] ?? $this->tier;
        $this->seats = $license['seats'] ?? $this->seats;
        $this->expires_at = $expires;
        $this->updates_until = isset($license['updates_until']) ? Carbon::parse($license['updates_until']) : $this->updates_until;
        $this->status = match (true) {
            ($license['status'] ?? 'active') === 'revoked' => self::REVOKED,
            $expires !== null && $expires->isPast() => self::EXPIRED,
            default => self::ACTIVE,
        };

        if ($activations !== null) {
            $this->activations_count = count(array_filter($activations, fn ($a) => empty($a['deactivated'])));
        }

        $this->last_synced_at = now();
        $this->sync_error = null;

        return $this;
    }

    public function logEvent(string $event, ?string $userId = null, ?string $detail = null): void
    {
        $this->events()->create(['event' => $event, 'user_id' => $userId, 'detail' => $detail]);
    }
}
