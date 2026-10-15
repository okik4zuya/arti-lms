<?php

namespace App\Actions\Licensing;

/** Input for IssueLicense. `orderRef` is the idempotency key (the gateway order id once purchases are automatic). */
final class IssueRequest
{
    public function __construct(
        public readonly string $email,
        public readonly ?string $name,
        public readonly string $tier,
        public readonly int $months,
        public readonly string $orderRef,
        public readonly ?int $seats = null,
        public readonly ?int $amountIdr = null,
        public readonly string $source = 'manual',
        public readonly ?string $buyerWhatsapp = null,
        public readonly ?string $notes = null,
        public readonly ?string $userId = null,
        public readonly ?string $issuedBy = null,
    ) {}
}
