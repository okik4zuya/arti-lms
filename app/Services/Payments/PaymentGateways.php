<?php

namespace App\Services\Payments;

use InvalidArgumentException;

/** Picks the payment gateway: the configured one for new orders, the order's own for an order that already exists. */
class PaymentGateways
{
    public const DRIVERS = ['midtrans' => MidtransClient::class, 'xendit' => XenditClient::class, 'mayar' => MayarClient::class];

    public function current(): PaymentGateway
    {
        return $this->for((string) config('services.payments.driver'));
    }

    public function for(string $name): PaymentGateway
    {
        $class = self::DRIVERS[$name] ?? null;

        if ($class === null) {
            throw new InvalidArgumentException("Unknown payment gateway \"{$name}\".");
        }

        return app($class);
    }
}
