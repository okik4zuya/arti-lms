<?php

namespace App\Actions\Orders;

use App\Models\LicenseIssuance;
use App\Models\Order;
use App\Services\Payments\PaymentGateway;
use App\Support\Offer;
use App\Support\Quota;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Starts a purchase: reserves the price (and the place in the launch batch or promotion), then asks the gateway for
 * the payment page. The reserved price is what the gateway is asked for and what the webhook later checks, so a price
 * change or a sold-out batch never affects a payment already in progress.
 *
 * Two buyers racing for the last place: both pick the same number, the unique index lets one INSERT through
 * and the other retries with a fresh count, ending on the normal price.
 */
class CreateCheckout
{
    public function __construct(private readonly PaymentGateway $gateway, private readonly Offer $offer) {}

    /**
     * @throws AlreadyPurchasedException
     * @throws \App\Services\Payments\PaymentGatewayException when no payment page could be created (the reserved place is given back)
     */
    public function handle(string $name, string $email, ?string $whatsapp, ?string $method = null): Order
    {
        $email = mb_strtolower(trim($email));

        Quota::releaseExpired();

        $open = Order::where('email', $email)->where('type', 'purchase')->where('status', Order::PENDING)
            ->where('expires_at', '>', now())->whereNotNull('checkout_url')->latest()->first();

        // The payment page was built for one method; a buyer who now wants the other gets a fresh page, and the old
        // order closes (it keeps working if paid late, like any expired one).
        if ($open !== null && $open->method_choice === $method) {
            return $open;
        }

        if ($open !== null) {
            $open->transition([Order::PENDING], Order::EXPIRED, ['launch_slot' => null, 'promo_slot' => null]);
            $open->logEvent('method_changed', (string) $method);
        }

        if (LicenseIssuance::where('email', $email)->where('status', LicenseIssuance::ACTIVE)->exists()) {
            throw new AlreadyPurchasedException;
        }

        $order = $this->reserve($name, $email, $whatsapp, $method);

        try {
            $invoice = $this->gateway->createInvoice(
                $order,
                route('checkout.done', ['order' => $order->public_id]),
                route('checkout.done', ['order' => $order->public_id, 'gagal' => 1]),
                $this->windowMinutes() * 60,
                $method,
            );
        } catch (Throwable $e) {
            $order->transition([Order::PENDING], Order::FAILED, ['launch_slot' => null, 'promo_slot' => null]);
            $order->logEvent('checkout_failed', class_basename($e));

            throw $e;
        }

        $order->forceFill(['gateway_ref' => $invoice['id'], 'checkout_url' => $invoice['invoice_url']])->save();
        $order->logEvent('checkout_created', $order->price_tier);

        return $order;
    }

    private function reserve(string $name, string $email, ?string $whatsapp, ?string $method): Order
    {
        for ($attempt = 1; ; $attempt++) {
            try {
                return DB::transaction(fn () => $this->place($name, $email, $whatsapp, $method));
            } catch (UniqueConstraintViolationException $e) {
                // Another payment took the place we picked; count again. Real duplicates of other columns are not retried.
                if ($attempt >= 5 || ! str_contains($e->getMessage(), 'slot')) {
                    throw $e;
                }
            }
        }
    }

    private function place(string $name, string $email, ?string $whatsapp, ?string $method): Order
    {
        $attributes = ['tier' => 'normal', 'amount' => $this->offer->normalPrice(), 'launch' => null, 'promotion' => null, 'promo' => null];

        $promotion = $this->offer->promotion();
        $promoSlot = $promotion?->quota !== null ? Quota::lowestFreePromoSlot($promotion) : null;
        $launchSlot = Quota::lowestFreeLaunchSlot(max(0, $this->offer->quota() - $this->offer->manualSold()));

        if ($promotion !== null && ($promotion->quota === null || $promoSlot !== null)) {
            $attributes = ['tier' => 'promo', 'amount' => $promotion->price, 'launch' => null, 'promotion' => $promotion->id, 'promo' => $promoSlot];
        } elseif ($launchSlot !== null) {
            $attributes = ['tier' => 'launch', 'amount' => $this->offer->launchPrice(), 'launch' => $launchSlot, 'promotion' => null, 'promo' => null];
        }

        return Order::create([
            'type' => 'purchase',
            'name' => $name,
            'email' => $email,
            'whatsapp' => $whatsapp,
            'method_choice' => $method,
            'amount' => $attributes['amount'],
            'price_tier' => $attributes['tier'],
            'launch_slot' => $attributes['launch'],
            'promotion_id' => $attributes['promotion'],
            'promo_slot' => $attributes['promo'],
            'status' => Order::PENDING,
            'gateway' => $this->gateway->name(),
            'expires_at' => now()->addMinutes($this->windowMinutes()),
        ]);
    }

    private function windowMinutes(): int
    {
        return max(15, (int) config('site.offer.payment_window_minutes'));
    }
}
