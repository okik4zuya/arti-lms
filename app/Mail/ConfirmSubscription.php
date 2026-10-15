<?php

namespace App\Mail;

use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

/**
 * The double opt-in message. It also carries the link to the free chapter the visitor was reading.
 * Queued, so the sign-up answers at once; the signed links are built when the job runs, so the
 * 7-day window starts at sending time.
 */
class ConfirmSubscription extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    /** @var list<int> Seconds to wait before each retry. */
    public array $backoff = [60, 300];

    public function __construct(public Subscriber $subscriber) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Konfirmasi: kabar dari ARTi');
    }

    public function content(): Content
    {
        $data = [
            'confirmUrl' => URL::temporarySignedRoute('subscribe.confirm', now()->addDays(7), ['subscriber' => $this->subscriber]),
            'unsubscribeUrl' => URL::signedRoute('subscribe.unsubscribe', ['subscriber' => $this->subscriber]),
            'readUrl' => route('free.start'),
            'fromPdf' => $this->subscriber->source === 'artipdf',
            'pdfUrl' => route('artipdf'),
        ];

        return new Content(view: 'mail.confirm-subscription', text: 'mail.confirm-subscription-text', with: $data);
    }
}
