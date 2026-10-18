<?php

namespace App\Support;

use App\Mail\PurchaseMail;
use App\Models\LicenseIssuance;

/** The WhatsApp message that delivers a license key to the buyer. */
class LicenseMessage
{
    /**
     * @param  ?string  $setPasswordUrl  the one-time set-password link that was just made (new account or a new link
     *                                   requested). It is never stored: it exists only in the modal that opens right
     *                                   after, so a message opened later from the table has no link in it. No password
     *                                   is ever part of the message.
     */
    public static function for(LicenseIssuance $license, ?string $setPasswordUrl = null): string
    {
        return strtr((string) config('site.license.whatsapp_template'), [
            '{name}' => $license->name ?: 'Kak',
            '{key}' => (string) $license->key,
            '{seats}' => (string) ($license->seats ?? 1),
            '{expires}' => $license->validityLabel(),
            '{installer}' => PurchaseMail::downloadPageUrl(),
            '{account}' => static::account($license, $setPasswordUrl),
        ]);
    }

    /**
     * The login block for the learner account: the email and the one-time link when one was just made, only the email
     * (the existing password still works) otherwise, nothing when the license has no account.
     */
    private static function account(LicenseIssuance $license, ?string $setPasswordUrl): string
    {
        $user = $license->user;

        if ($user === null) {
            return '';
        }

        $template = filled($setPasswordUrl)
            ? config('site.license.account_template')
            : config('site.license.account_existing_template');

        return strtr((string) $template, [
            '{email}' => $user->email,
            '{password_link}' => (string) $setPasswordUrl,
            '{login_url}' => route('login'),
        ]);
    }

    /** wa.me link to the buyer's number, or null when none was entered. */
    public static function url(LicenseIssuance $license, ?string $setPasswordUrl = null): ?string
    {
        return $license->buyer_whatsapp
            ? Offer::whatsappUrl(static::for($license, $setPasswordUrl), $license->buyer_whatsapp)
            : null;
    }
}
