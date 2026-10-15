<?php

namespace App\Support;

use App\Models\LicenseIssuance;

/** The WhatsApp message that delivers a license key to the buyer. */
class LicenseMessage
{
    /**
     * @param  ?string  $accountPassword  a learner password that was just set (new account or reset). It is never
     *                                    stored: it exists only in the modal that opens right after, so the message
     *                                    opened later from the table has no password in it.
     */
    public static function for(LicenseIssuance $license, ?string $accountPassword = null): string
    {
        $installer = config('services.licensing.installer_url') ?: '(tautan installer menyusul)';

        return strtr((string) config('site.license.whatsapp_template'), [
            '{name}' => $license->name ?: 'Kak',
            '{key}' => (string) $license->key,
            '{seats}' => (string) ($license->seats ?? 1),
            '{expires}' => $license->expires_at?->translatedFormat('j F Y') ?? '-',
            '{installer}' => $installer,
            '{account}' => static::account($license, $accountPassword),
        ]);
    }

    /**
     * The login block for the learner account: email and password when one was just set, only the email
     * (the existing password still works) otherwise, nothing when the license has no account.
     */
    private static function account(LicenseIssuance $license, ?string $accountPassword): string
    {
        $user = $license->user;

        if ($user === null) {
            return '';
        }

        $template = filled($accountPassword)
            ? config('site.license.account_template')
            : config('site.license.account_existing_template');

        return strtr((string) $template, [
            '{email}' => $user->email,
            '{password}' => (string) $accountPassword,
            '{login_url}' => route('login'),
        ]);
    }

    /** wa.me link to the buyer's number, or null when none was entered. */
    public static function url(LicenseIssuance $license, ?string $accountPassword = null): ?string
    {
        return $license->buyer_whatsapp
            ? Offer::whatsappUrl(static::for($license, $accountPassword), $license->buyer_whatsapp)
            : null;
    }
}
