<?php

namespace App\Services\Licensing;

use RuntimeException;

/** A failed call to the licensing Worker. `code` is the Worker's error code, or one of ours (unreachable, not_configured). */
class LicenseApiException extends RuntimeException
{
    public function __construct(
        public readonly string $errorCode,
        string $message,
        public readonly int $status = 0,
        public readonly array $extra = [],
    ) {
        parent::__construct($message);
    }

    /** The Worker could not be reached or timed out: nothing is known about whether it acted. */
    public function isUnreachable(): bool
    {
        return in_array($this->errorCode, ['unreachable', 'server_error'], true) || $this->status >= 500;
    }

    public function isOrderExists(): bool
    {
        return $this->errorCode === 'order_exists';
    }

    public function isNotFound(): bool
    {
        return $this->errorCode === 'not_found';
    }
}
