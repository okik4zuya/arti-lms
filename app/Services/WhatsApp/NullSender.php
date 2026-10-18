<?php

namespace App\Services\WhatsApp;

/** The default: no provider chosen, so no message is sent and the order is unaffected. */
class NullSender implements WhatsAppSender
{
    public function enabled(): bool
    {
        return false;
    }

    public function send(string $number, string $message): void {}
}
