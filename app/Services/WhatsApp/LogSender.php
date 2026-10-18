<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Facades\Log;

/** Development driver: proves the stage ran without exposing the message. Logs the last 4 digits and the length only. */
class LogSender implements WhatsAppSender
{
    public function enabled(): bool
    {
        return true;
    }

    public function send(string $number, string $message): void
    {
        Log::info('whatsapp (log driver): message not sent', ['to' => '***'.substr($number, -4), 'length' => mb_strlen($message)]);
    }
}
