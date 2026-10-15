<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * A mailing-list sign-up. Double opt-in: only rows with `confirmed_at` and no `unsubscribed_at`
 * may receive mail beyond the confirmation message.
 */
class Subscriber extends Model
{
    /** Sign-up places allowed in the form, with the label the admin sees. */
    public const SOURCES = [
        'artipdf' => 'ARTi PDF (unduhan)',
        'baca-1' => 'Baca gratis, halaman 1',
        'baca-2' => 'Baca gratis, halaman 2',
        'baca-3' => 'Baca gratis, halaman 3',
        'baca-4' => 'Baca gratis, halaman 4',
    ];

    public const PENDING = 'pending';

    public const ACTIVE = 'active';

    public const UNSUBSCRIBED = 'unsubscribed';

    /** Minutes before the confirmation email may be sent to the same unconfirmed address again. */
    public const RESEND_AFTER_MINUTES = 10;

    protected $fillable = ['email', 'source', 'consented_at'];

    protected function casts(): array
    {
        return [
            'consented_at' => 'datetime',
            'confirmation_sent_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
            'buyer_at' => 'datetime',
        ];
    }

    /** Confirmed and not unsubscribed: the only rows that may be mailed. */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotNull('confirmed_at')->whereNull('unsubscribed_at');
    }

    /** Signed up but has not clicked the confirmation link. */
    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('confirmed_at')->whereNull('unsubscribed_at');
    }

    public function scopeUnsubscribed(Builder $query): Builder
    {
        return $query->whereNotNull('unsubscribed_at');
    }

    public function status(): string
    {
        return match (true) {
            $this->unsubscribed_at !== null => self::UNSUBSCRIBED,
            $this->confirmed_at !== null => self::ACTIVE,
            default => self::PENDING,
        };
    }

    public function isActive(): bool
    {
        return $this->status() === self::ACTIVE;
    }

    public function canResendConfirmation(): bool
    {
        return $this->confirmation_sent_at === null
            || $this->confirmation_sent_at->lt(now()->subMinutes(self::RESEND_AFTER_MINUTES));
    }

    /** Marks the address as a buyer (no more sales mail). Matches case-insensitively; no row, no effect. */
    public static function markBuyer(string $email): void
    {
        static::query()
            ->whereRaw('lower(email) = ?', [mb_strtolower(trim($email))])
            ->whereNull('buyer_at')
            ->update(['buyer_at' => now()]);
    }

    /** Marks every subscriber whose email matches a license that really exists at the Worker. Returns how many changed. */
    public static function syncBuyers(): int
    {
        $emails = LicenseIssuance::sold()->pluck('email')->map(fn ($e) => mb_strtolower(trim($e)))->unique()->all();

        $changed = 0;
        foreach (array_chunk($emails, 500) as $chunk) {
            $changed += static::query()
                ->whereIn(\Illuminate\Support\Facades\DB::raw('lower(email)'), $chunk)
                ->whereNull('buyer_at')
                ->update(['buyer_at' => now()]);
        }

        return $changed;
    }
}
