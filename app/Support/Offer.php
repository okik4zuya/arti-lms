<?php

namespace App\Support;

/**
 * The landing page offer: launch price for the first buyers, then the normal price.
 * Reads config('site.offer'); the sold counter is whatever the admin entered, never estimated.
 */
class Offer
{
    public function normalPrice(): int
    {
        return (int) config('site.offer.price_normal');
    }

    public function launchPrice(): int
    {
        return (int) config('site.offer.price_launch');
    }

    public function quota(): int
    {
        return (int) config('site.offer.launch_quota');
    }

    public function guaranteeDays(): int
    {
        return (int) config('site.offer.guarantee_days');
    }

    /** Sold count, or null when the admin has not set it (counter hidden). */
    public function sold(): ?int
    {
        $sold = config('site.offer.launch_sold');

        return is_numeric($sold) ? max(0, (int) $sold) : null;
    }

    public function remaining(): ?int
    {
        $sold = $this->sold();

        return $sold === null ? null : max(0, $this->quota() - $sold);
    }

    /** The launch price is on unless the counter says the quota is used up. */
    public function launchActive(): bool
    {
        return $this->remaining() !== 0;
    }

    public function currentPrice(): int
    {
        return $this->launchActive() ? $this->launchPrice() : $this->normalPrice();
    }

    public function perMonthNormal(): int
    {
        return (int) (round($this->normalPrice() / 12 / 100) * 100);
    }

    public function format(int $amount): string
    {
        return 'Rp'.number_format($amount, 0, ',', '.');
    }

    /** WhatsApp link with a pre-filled message, or the contact page while no number is configured. */
    public function buyUrl(): string
    {
        $number = preg_replace('/\D+/', '', (string) config('site.contact_whatsapp'));

        if ($number === '') {
            return route('kontak');
        }

        return 'https://wa.me/'.$number.'?text='.rawurlencode((string) config('site.offer.whatsapp_message'));
    }
}
