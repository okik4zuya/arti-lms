<?php

namespace App\Support;

use App\Models\Promotion;

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

    /**
     * Places already used in the launch batch: sales made outside the gateway (the admin's own count in
     * `launch_sold`, e.g. earlier WhatsApp sales) plus every order that holds a launch place right now.
     */
    public function sold(): int
    {
        return $this->manualSold() + Quota::launchHeld();
    }

    /** Sales made outside the gateway, entered by the admin. */
    public function manualSold(): int
    {
        $manual = config('site.offer.launch_sold');

        return is_numeric($manual) ? max(0, (int) $manual) : 0;
    }

    public function remaining(): int
    {
        return max(0, $this->quota() - $this->sold());
    }

    /** The launch price is on while places are left. */
    public function launchActive(): bool
    {
        return $this->remaining() > 0;
    }

    /** The running promotion with a place left (the cheapest one if several overlap), or null. */
    public function promotion(): ?Promotion
    {
        return Promotion::running()->orderBy('price')->get()
            ->first(fn (Promotion $p) => $p->quota === null || Quota::promoHeld($p) < $p->quota);
    }

    /** promo | launch | normal: promotion beats the launch batch, which beats the normal price. */
    public function tier(): string
    {
        return match (true) {
            $this->promotion() !== null => 'promo',
            $this->launchActive() => 'launch',
            default => 'normal',
        };
    }

    public function currentPrice(): int
    {
        return match ($this->tier()) {
            'promo' => (int) $this->promotion()->price,
            'launch' => $this->launchPrice(),
            default => $this->normalPrice(),
        };
    }
    public function perMonthNormal(): int
    {
        return (int) (round($this->normalPrice() / 12 / 100) * 100);
    }

    public function format(int $amount): string
    {
        return 'Rp'.number_format($amount, 0, ',', '.');
    }

    /** The WhatsApp number in international digits (a leading 0 becomes 62), or null when none is set. */
    public static function whatsappNumber(): ?string
    {
        return static::normalizeNumber((string) config('site.contact_whatsapp'));
    }

    /** Any phone number in international digits (a leading 0 becomes 62), or null when empty. */
    public static function normalizeNumber(?string $raw): ?string
    {
        $number = preg_replace('/\D+/', '', (string) $raw);

        if ($number === '') {
            return null;
        }

        return str_starts_with($number, '0') ? '62'.substr($number, 1) : $number;
    }

    /** wa.me link for a message (to the site number unless another is given), or null while no number is known. */
    public static function whatsappUrl(string $message, ?string $number = null): ?string
    {
        $number = $number !== null ? static::normalizeNumber($number) : static::whatsappNumber();

        return $number ? 'https://wa.me/'.$number.'?text='.rawurlencode($message) : null;
    }

    /** WhatsApp link with a pre-filled message, or the contact page while no number is configured. */
    public function buyUrl(): string
    {
        return static::whatsappUrl((string) config('site.offer.whatsapp_message')) ?? route('kontak');
    }
}
