<?php

namespace App\Support;

use InvalidArgumentException;

/** Preset reasons for license actions, so the audit log stays uniform. "Lainnya" requires free text. */
final class LicenseReasons
{
    public const OTHER = 'Lainnya';

    public const EXTEND_COMPENSATION = ['Gangguan layanan', 'Bug aplikasi', self::OTHER];

    public const RELEASE_DEVICE = ['Ganti komputer', 'Komputer rusak atau hilang', 'Instal ulang', self::OTHER];

    public const ROTATE_KEY = ['Key bocor', 'Key hilang', self::OTHER];

    public const REVOKE = ['Refund', 'Penyalahgunaan', 'Pembayaran gagal', self::OTHER];

    public const UNDO_REVOKE = ['Salah cabut', 'Refund dibatalkan', self::OTHER];

    /** @return array<string,string> value => label, for a Select */
    public static function options(array $reasons): array
    {
        return array_combine($reasons, $reasons);
    }

    /** "Reason" or "Reason: detail". Refuses an unknown reason, and "Lainnya" without detail. */
    public static function compose(array $allowed, ?string $reason, ?string $detail): string
    {
        $reason = trim((string) $reason);
        $detail = trim((string) $detail);

        if (! in_array($reason, $allowed, true)) {
            throw new InvalidArgumentException('Alasan wajib dipilih.');
        }

        if ($reason === self::OTHER && $detail === '') {
            throw new InvalidArgumentException('Alasan "Lainnya" wajib dijelaskan.');
        }

        return $detail === '' ? $reason : "{$reason}: {$detail}";
    }
}
