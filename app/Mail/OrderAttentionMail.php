<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Tells the admins that an order needs a human. Carries the order reference and a note code, nothing secret. */
class OrderAttentionMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    /** @var list<int> */
    public array $backoff = [60, 300];

    public function __construct(public readonly string $orderId, public readonly string $note) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Pesanan ARTi perlu dicek');
    }

    public function content(): Content
    {
        $order = Order::find($this->orderId);

        return new Content(text: 'mail.order-attention', with: [
            'reference' => $order?->public_id ?? $this->orderId,
            'amount' => $order?->amount,
            'email' => $order?->email,
            'note' => $this->note,
        ]);
    }
}
