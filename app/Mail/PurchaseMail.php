<?php

namespace App\Mail;

use App\Models\LicenseIssuance;
use App\Models\Order;
use App\Support\MailText;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The purchase mail: learner account, one-time set-password link, license key and download link.
 * A password is never part of it. The key is read (decrypted) when the mail is built, so the queue payload
 * holds only the order id and the link. The link is null for a buyer who already had an account.
 */
class PurchaseMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    /** @var list<int> */
    public array $backoff = [60, 300];

    public function __construct(public readonly string $orderId, public readonly ?string $setPasswordUrl) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Pembelian ARTi berhasil: akun, kunci lisensi, dan unduhan');
    }

    public function content(): Content
    {
        $order = Order::findOrFail($this->orderId);
        /** @var LicenseIssuance $license */
        $license = $order->licenseIssuance()->firstOrFail();

        $html = view('mail.purchase', [
            'name' => $order->name,
            'email' => $order->email,
            'key' => (string) $license->key,
            'seats' => $license->seats ?? 1,
            'validity' => $license->validityLabel(),
            'setPasswordUrl' => $this->setPasswordUrl,
            'loginUrl' => route('login'),
            'forgotUrl' => route('password.request'),
            'downloadPageUrl' => static::downloadPageUrl(),
        ])->render();

        return new Content(htmlString: $html, text: 'mail.plain', with: ['text' => MailText::fromHtml($html)]);
    }

    /** Where the buyer downloads the installer: the course page behind login, never a direct link in a message. */
    public static function downloadPageUrl(): string
    {
        return route('content.show', ['course' => config('site.offer.course_slug'), 'slug' => 'download-installer']);
    }
}
