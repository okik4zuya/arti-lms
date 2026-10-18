<?php

namespace App\Actions\Orders;

use App\Actions\Account\IssueSetPasswordLink;
use App\Mail\PurchaseMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;

/**
 * Sends the purchase mail again with a fresh set-password link (the old link, if still alive, stops working:
 * the broker keeps one token per account). Used when the buyer lost the mail or the link expired.
 */
class ResendPurchaseMail
{
    public function __construct(private readonly IssueSetPasswordLink $links) {}

    public function handle(Order $order, User $actor): void
    {
        if (! $actor->can('order.resend')) {
            throw new AuthorizationException;
        }

        if ($order->status !== Order::FULFILLED || $order->licenseIssuance === null || $order->user === null) {
            throw new InvalidArgumentException('Hanya pesanan yang sudah selesai diproses yang bisa dikirim ulang.');
        }

        if ($order->user->hasAnyRole(['admin', 'support'])) {
            throw new InvalidArgumentException('Akun admin atau support tidak memakai tautan atur password.');
        }

        Mail::to($order->email)->queue(new PurchaseMail($order->id, $this->links->handle($order->user, IssueSetPasswordLink::WELCOME)));
        $order->forceFill(['email_sent_at' => now()])->save();
        $order->logEvent('email_resent', null, $actor->getKey());
    }
}
