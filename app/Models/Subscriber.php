<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A mailing-list sign-up. Double opt-in: only rows with `confirmed_at` and no `unsubscribed_at`
 * may receive mail beyond the confirmation message.
 */
class Subscriber extends Model
{
    /** Sign-up places allowed in the form; the free-chapter ones carry the page number. */
    public const SOURCES = ['artipdf', 'baca-1', 'baca-2', 'baca-3', 'baca-4'];

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

    public function isActive(): bool
    {
        return $this->confirmed_at !== null && $this->unsubscribed_at === null;
    }

    public function canResendConfirmation(): bool
    {
        return $this->confirmation_sent_at === null
            || $this->confirmation_sent_at->lt(now()->subMinutes(self::RESEND_AFTER_MINUTES));
    }
}
