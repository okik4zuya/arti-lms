<?php

namespace App\Support;

use App\Models\LicenseIssuance;

/** The WhatsApp message that delivers a license key to the buyer. */
class LicenseMessage
{
    public static function for(LicenseIssuance $license): string
    {
        $installer = config('services.licensing.installer_url') ?: '(tautan installer menyusul)';

        return strtr((string) config('site.license.whatsapp_template'), [
            '{name}' => $license->name ?: 'Kak',
            '{key}' => (string) $license->key,
            '{tier}' => $license->tier,
            '{seats}' => (string) ($license->seats ?? 1),
            '{expires}' => $license->expires_at?->translatedFormat('j F Y') ?? '-',
            '{installer}' => $installer,
        ]);
    }

    /** wa.me link to the buyer's number, or null when none was entered. */
    public static function url(LicenseIssuance $license): ?string
    {
        return $license->buyer_whatsapp
            ? Offer::whatsappUrl(static::for($license), $license->buyer_whatsapp)
            : null;
    }
}
