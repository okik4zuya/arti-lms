<?php

namespace App\Support;

use App\Mail\PurchaseMail;
use App\Models\Order;

/** The purchase WhatsApp text. Same facts as the mail: account email, one-time link, key, download. No password. */
class OrderWhatsAppMessage
{
    public static function for(Order $order, ?string $setPasswordUrl): string
    {
        $license = $order->licenseIssuance;
        $link = $setPasswordUrl
            ? "Atur password Anda lewat tautan sekali pakai ini (tanpa batas waktu, berlaku sampai password diatur):\n{$setPasswordUrl}\n\n"
            : 'Akun ini sudah ada sebelumnya, password lama Anda tetap berlaku. Lupa? Pakai "Lupa password?" di '.route('login').".\n\n";
        return strtr((string) config('site.license.whatsapp_purchase_template'), [
            '{name}' => $order->name ?: 'Kak',
            '{email}' => $order->email,
            '{password_link}' => $link,
            '{key}' => (string) $license->key,
            '{seats}' => (string) ($license->seats ?? 1),
            '{validity}' => $license->validityLabel(),
            '{installer}' => PurchaseMail::downloadPageUrl(),
        ]);
    }
}
