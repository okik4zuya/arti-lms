<?php

namespace App\Actions\Licensing;

use App\Models\LicenseIssuance;
use App\Models\User;
use App\Services\Licensing\LicenseApi;
use App\Support\LicenseReasons;
use Illuminate\Auth\Access\AuthorizationException;
use InvalidArgumentException;

/**
 * Replaces the key (same key_id, activations kept). The old key stops working at once. The new key is
 * stored encrypted and the "message sent" flag is cleared so the new key gets sent.
 */
class RotateKey
{
    public function __construct(private readonly LicenseApi $api) {}

    public function handle(LicenseIssuance $row, User $actor, ?string $reason, ?string $detail = null): LicenseIssuance
    {
        if (! $actor->can('license.rotate_key')) {
            throw new AuthorizationException;
        }

        if ($row->key_id === null || $row->isPending()) {
            throw new InvalidArgumentException('Lisensi belum terbit di Worker.');
        }

        $why = LicenseReasons::compose(LicenseReasons::ROTATE_KEY, $reason, $detail);

        $rotated = $this->api->resend($row->key_id, $why);

        if (blank($rotated['key'] ?? null)) {
            throw new InvalidArgumentException('Worker tidak mengembalikan key baru. Sinkronkan dan periksa status key.');
        }

        $row->key = $rotated['key'];
        $row->key_stale = false;
        $row->key_checked_at = now();
        $row->wa_sent_at = null;
        $row->save();
        $row->logEvent('key_rotated', $actor->getKey(), $why);

        return $row;
    }
}
