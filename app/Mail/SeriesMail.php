<?php

namespace App\Mail;

use App\Models\Subscriber;
use App\Support\EmailSeries;
use App\Support\Offer;
use Illuminate\Mail\Mailables\Envelope;

/** One email of the welcome series (see EmailSeries). The copy is in resources/views/mail/series/{step}.blade.php. */
class SeriesMail extends ListMailable
{
    public function __construct(public Subscriber $subscriber, public int $step) {}

    protected function subscriber(): ?Subscriber
    {
        return $this->subscriber;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: EmailSeries::subject($this->step));
    }

    protected function bodyView(): string
    {
        return 'mail.series.'.$this->step;
    }

    protected function bodyData(): array
    {
        $offer = app(Offer::class);

        return [
            'subject' => EmailSeries::subject($this->step),
            'launchActive' => $offer->launchActive(),
            'launchPrice' => $offer->format($offer->launchPrice()),
            'normalPrice' => $offer->format($offer->normalPrice()),
            'perMonth' => $offer->format($offer->perMonthNormal()),
            'quota' => $offer->quota(),
            'remaining' => $offer->remaining(),
            'guaranteeDays' => $offer->guaranteeDays(),
            'buyUrl' => $offer->buyUrl(),
        ];
    }
}
