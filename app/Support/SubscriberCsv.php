<?php

namespace App\Support;

use App\Models\Subscriber;

/** CSV of the people who may be mailed: confirmed and not unsubscribed. Nothing else leaves the database. */
final class SubscriberCsv
{
    public const HEADER = ['email', 'sumber', 'dikonfirmasi', 'pembeli'];

    /** @param  resource  $handle */
    public static function write($handle): void
    {
        fputcsv($handle, self::HEADER);

        Subscriber::active()->orderBy('id')->lazyById(500)->each(function (Subscriber $subscriber) use ($handle) {
            fputcsv($handle, [
                self::safe($subscriber->email),
                $subscriber->source,
                $subscriber->confirmed_at?->format('Y-m-d H:i'),
                $subscriber->buyer_at ? 'ya' : 'tidak',
            ]);
        });
    }

    /** A cell that starts with = + - @ would be run as a formula by Excel or Sheets; a leading quote makes it text. */
    public static function safe(string $value): string
    {
        return preg_match('/^[=+\-@\t\r]/', $value) ? "'".$value : $value;
    }
}
