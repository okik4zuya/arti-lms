<?php

namespace App\Mail;

use App\Models\Broadcast;
use App\Models\Subscriber;
use App\Support\MarkdownRenderer;
use Illuminate\Mail\Mailables\Envelope;

/**
 * A broadcast written in Markdown by an admin. A test copy (no subscriber) has no working unsubscribe link
 * and a [UJI] subject, so it can never be mistaken for the real send.
 */
class BroadcastMail extends ListMailable
{
    public function __construct(public ?Subscriber $subscriber, public Broadcast $broadcast, public bool $test = false) {}

    protected function subscriber(): ?Subscriber
    {
        return $this->subscriber;
    }

    protected function subjectLine(): string
    {
        return ($this->test ? '[UJI] ' : '').$this->broadcast->subject;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subjectLine());
    }

    protected function bodyView(): string
    {
        return 'mail.broadcast';
    }

    protected function bodyData(): array
    {
        return [
            'subject' => $this->subjectLine(),
            'html' => app(MarkdownRenderer::class)->toHtml($this->broadcast->body),
        ];
    }
}
