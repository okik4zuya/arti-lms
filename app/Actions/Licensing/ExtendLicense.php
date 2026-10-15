<?php

namespace App\Actions\Licensing;

use App\Models\LicenseIssuance;
use App\Models\User;
use App\Services\Licensing\LicenseApi;
use App\Support\LicenseReasons;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

/**
 * Adds months to a license. Two kinds: `paid` (a renewal, reference may follow) and `compensation`
 * (a goodwill month, reason required). The Worker's `extend` is not idempotent, so a lock stops a
 * double click from adding twice, and only positive months up to MAX_MONTHS are accepted.
 */
class ExtendLicense
{
    public const PAID = 'paid';

    public const COMPENSATION = 'compensation';

    public const MAX_MONTHS = 12;

    public const MAX_COMPENSATION_MONTHS = 1;

    public function __construct(private readonly LicenseApi $api) {}

    /**
     * @throws AuthorizationException
     * @throws InvalidArgumentException  a rule was broken (nothing was sent to the Worker)
     * @throws \App\Services\Licensing\LicenseApiException  the Worker refused or could not be reached; when it was
     *                                                       unreachable the outcome is unknown, so sync before retrying
     */
    public function handle(LicenseIssuance $row, User $actor, string $kind, int $months, ?string $paymentRef = null, ?string $reason = null, ?string $detail = null): LicenseIssuance
    {
        if (! $actor->can('license.extend')) {
            throw new AuthorizationException;
        }

        if ($row->key_id === null || $row->isPending()) {
            throw new InvalidArgumentException('Lisensi belum terbit di Worker.');
        }

        if (! in_array($kind, [self::PAID, self::COMPENSATION], true)) {
            throw new InvalidArgumentException('Jenis perpanjangan tidak dikenal.');
        }

        if ($months < 1 || $months > self::MAX_MONTHS) {
            throw new InvalidArgumentException('Perpanjangan harus 1 sampai '.self::MAX_MONTHS.' bulan.');
        }

        $compensation = $kind === self::COMPENSATION;

        if ($compensation && $months > self::MAX_COMPENSATION_MONTHS) {
            throw new InvalidArgumentException('Kompensasi maksimal '.self::MAX_COMPENSATION_MONTHS.' bulan.');
        }

        $why = $compensation ? LicenseReasons::compose(LicenseReasons::EXTEND_COMPENSATION, $reason, $detail) : null;
        $paymentRef = filled($paymentRef) ? trim($paymentRef) : null;

        $lock = Cache::lock('license-extend:'.$row->getKey(), 30);

        if (! $lock->get()) {
            throw new InvalidArgumentException('Perpanjangan untuk lisensi ini sedang diproses. Tunggu sebentar, lalu Sinkronkan untuk memeriksa tanggalnya.');
        }

        try {
            $result = $this->api->extend($row->key_id, $months);

            $row->expires_at = Carbon::parse($result['expires']);
            $row->updates_until = isset($result['updates_until']) ? Carbon::parse($result['updates_until']) : $row->updates_until;

            if ($row->status === LicenseIssuance::EXPIRED && $row->expires_at->isFuture()) {
                $row->status = LicenseIssuance::ACTIVE;
            }

            $note = match (true) {
                $compensation => "Perpanjangan kompensasi +{$months} bulan ({$why})",
                $paymentRef === null => "Perpanjangan berbayar +{$months} bulan tanpa referensi pembayaran",
                default => null,
            };

            if ($note !== null) {
                $row->review_note = filled($row->review_note) ? $row->review_note.'; '.$note : $note;
            }

            $row->save();
            $row->logEvent('extended', $actor->getKey(), $compensation
                ? "+{$months} bulan, kompensasi: {$why}"
                : "+{$months} bulan, berbayar".($paymentRef !== null ? ", ref {$paymentRef}" : ', tanpa referensi'));
        } finally {
            $lock->release();
        }

        return $row;
    }
}
