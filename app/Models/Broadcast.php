<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A one-off email to the mailing list, written in Markdown by an admin. */
class Broadcast extends Model
{
    public const DRAFT = 'draft';

    public const SENDING = 'sending';

    public const SENT = 'sent';

    public const NON_BUYERS = 'non_buyers';

    public const ALL_ACTIVE = 'all_active';

    public const AUDIENCES = [
        self::NON_BUYERS => 'Belum membeli (untuk penawaran)',
        self::ALL_ACTIVE => 'Semua yang aktif (untuk kabar umum)',
    ];

    // created_by is set by the create page from the logged-in admin, never from the form.
    protected $fillable = ['subject', 'body', 'audience', 'created_by'];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isDraft(): bool
    {
        return $this->status === self::DRAFT;
    }

    /**
     * Who may receive this broadcast. Always confirmed and not unsubscribed; buyers are left out unless the
     * audience says everyone. People who already have this broadcast queued are left out too, so a repeated
     * or resumed send never mails anyone twice.
     */
    public function recipients(): Builder
    {
        return Subscriber::query()
            ->active()
            ->when($this->audience === self::NON_BUYERS, fn (Builder $query) => $query->whereNull('buyer_at'))
            ->whereNotIn('id', fn ($query) => $query->select('subscriber_id')->from('broadcast_deliveries')->where('broadcast_id', $this->id));
    }
}
