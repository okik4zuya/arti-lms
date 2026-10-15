<?php

namespace App\Actions\Licensing;

use App\Models\LicenseIssuance;
use App\Models\User;
use App\Services\Licensing\LicenseApi;
use App\Support\LicenseReasons;
use Illuminate\Auth\Access\AuthorizationException;
use InvalidArgumentException;

/**
 * Admin only. Revoking stops renewals and new activations; machines that are already activated keep
 * their signed license until it expires (accepted product decision). The email must be typed to confirm.
 */
class RevokeLicense
{
    public function __construct(private readonly LicenseApi $api) {}

    public function handle(LicenseIssuance $row, User $actor, ?string $reason, ?string $detail = null, ?string $confirmEmail = null, bool $undo = false): LicenseIssuance
    {
        if (! $actor->can('license.revoke')) {
            throw new AuthorizationException;
        }

        if ($row->key_id === null || $row->isPending()) {
            throw new InvalidArgumentException('Lisensi belum terbit di Worker.');
        }

        if ($undo && $row->status !== LicenseIssuance::REVOKED) {
            throw new InvalidArgumentException('Lisensi ini tidak sedang dicabut.');
        }

        if (! $undo && $row->status === LicenseIssuance::REVOKED) {
            throw new InvalidArgumentException('Lisensi ini sudah dicabut.');
        }

        $why = LicenseReasons::compose($undo ? LicenseReasons::UNDO_REVOKE : LicenseReasons::REVOKE, $reason, $detail);

        if (! $undo && strcasecmp(trim((string) $confirmEmail), $row->email) !== 0) {
            throw new InvalidArgumentException('Email konfirmasi tidak sama dengan email lisensi.');
        }

        $this->api->revoke($row->key_id, $why, $undo);

        $row->applyWorkerState(['status' => $undo ? 'active' : 'revoked']);
        $row->save();
        $row->logEvent($undo ? 'unrevoked' : 'revoked', $actor->getKey(), $why);

        return $row;
    }
}
