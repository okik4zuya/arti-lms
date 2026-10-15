<?php

namespace App\Actions\Licensing;

use App\Models\LicenseIssuance;
use App\Models\User;
use App\Services\Licensing\LicenseApi;
use App\Support\LicenseReasons;
use Illuminate\Auth\Access\AuthorizationException;
use InvalidArgumentException;

/**
 * Frees one activated machine so the buyer can activate another. Support may do it up to YEARLY_CAP
 * times per license per year (the same cap the Worker applies to self-service); beyond that only an admin.
 */
class ReleaseDevice
{
    public const YEARLY_CAP = 3;

    public function __construct(private readonly LicenseApi $api) {}

    public function handle(LicenseIssuance $row, User $actor, string $machineHash, ?string $reason, ?string $detail = null): LicenseIssuance
    {
        if (! $actor->can('license.release_device')) {
            throw new AuthorizationException;
        }

        if ($row->key_id === null || $row->isPending()) {
            throw new InvalidArgumentException('Lisensi belum terbit di Worker.');
        }

        $why = LicenseReasons::compose(LicenseReasons::RELEASE_DEVICE, $reason, $detail);

        if ($this->releasedThisYear($row) >= self::YEARLY_CAP && ! $actor->can('license.revoke')) {
            throw new InvalidArgumentException('Batas '.self::YEARLY_CAP.' pelepasan perangkat per tahun sudah tercapai. Minta admin.');
        }

        $result = $this->api->deactivate($row->key_id, $machineHash);

        if (empty($result['ok'])) {
            throw new InvalidArgumentException('Perangkat tidak ditemukan atau sudah dilepas. Sinkronkan lalu coba lagi.');
        }

        $row->activations_count = max(0, (int) $row->activations_count - 1);
        $row->save();
        $row->logEvent('device_released', $actor->getKey(), substr($machineHash, 0, 8).': '.$why);

        return $row;
    }

    public function releasedThisYear(LicenseIssuance $row): int
    {
        return $row->events()->where('event', 'device_released')->where('created_at', '>=', now()->subYear())->count();
    }
}
