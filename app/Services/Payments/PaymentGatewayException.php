<?php

namespace App\Services\Payments;

use RuntimeException;

/** A failed call to a payment gateway. `unreachable` means nothing is known about the outcome, so the caller may retry. */
class PaymentGatewayException extends RuntimeException
{
    public function __construct(public readonly string $errorCode, string $message, public readonly int $status = 0)
    {
        parent::__construct($message);
    }

    public function isUnreachable(): bool
    {
        return $this->errorCode === 'unreachable' || $this->status >= 500;
    }

    public function isNotFound(): bool
    {
        return $this->status === 404;
    }
}
