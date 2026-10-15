<?php

namespace App\Mail;

use App\Models\Subscriber;
use App\Support\MailText;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

/**
 * Base of every mail that goes to the mailing list after the confirmation: queued, HTML with a text twin,
 * and carrying the unsubscribe link in the footer plus the List-Unsubscribe headers (one-click) that mail
 * providers expect from bulk senders.
 */
abstract class ListMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    /** @var list<int> */
    public array $backoff = [60, 300];

    abstract protected function subscriber(): ?Subscriber;

    abstract protected function bodyView(): string;

    /** @return array<string, mixed> */
    abstract protected function bodyData(): array;

    protected function unsubscribeUrl(): ?string
    {
        $subscriber = $this->subscriber();

        return $subscriber ? URL::signedRoute('subscribe.unsubscribe', ['subscriber' => $subscriber]) : null;
    }

    public function content(): Content
    {
        $html = view($this->bodyView(), $this->bodyData() + [
            'unsubscribeUrl' => $this->unsubscribeUrl(),
            'consentedOn' => $this->subscriber()?->consented_at?->copy()->locale('id')->translatedFormat('j F Y'),
        ])->render();

        return new Content(htmlString: $html, text: 'mail.plain', with: ['text' => MailText::fromHtml($html)]);
    }

    public function headers(): Headers
    {
        $url = $this->unsubscribeUrl();

        return new Headers(text: $url ? [
            'List-Unsubscribe' => "<{$url}>",
            'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
        ] : []);
    }
}
