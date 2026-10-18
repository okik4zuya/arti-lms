<?php

namespace App\Services\WhatsApp;

/**
 * Sends one WhatsApp text. The provider is undecided (Cloud API or a third-party gateway), so everything
 * above this interface is provider-neutral. Implementations must never log the message: it carries a key.
 */
interface WhatsAppSender
{
    /** False for the `none` driver: the stage is skipped and nothing is claimed. */
    public function enabled(): bool;

    /**
     * @param  string  $number  international digits, e.g. 6281234567890
     *
     * @throws WhatsAppException when the provider refused or could not be reached
     */
    public function send(string $number, string $message): void;
}
