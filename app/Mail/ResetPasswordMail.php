<?php

namespace App\Mail;

use App\Support\MailText;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The "Lupa password?" mail: one signed link, nothing else. The link is built when the request is handled
 * and carries its own expiry, so a queue delay never extends it. Never contains a password.
 */
class ResetPasswordMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    /** @var list<int> */
    public array $backoff = [30, 120];

    public function __construct(public readonly string $url, public readonly int $minutes) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Atur ulang password ARTi');
    }

    public function content(): Content
    {
        $html = view('mail.reset-password', ['url' => $this->url, 'minutes' => $this->minutes])->render();

        return new Content(htmlString: $html, text: 'mail.plain', with: ['text' => MailText::fromHtml($html)]);
    }
}
