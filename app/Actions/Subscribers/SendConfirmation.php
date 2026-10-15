<?php

namespace App\Actions\Subscribers;

use App\Mail\ConfirmSubscription;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Mail;
use Throwable;

/** Queues the double opt-in email. Used by the public sign-up and by the admin "send again" action. */
class SendConfirmation
{
    /** @return bool false when the mail could not be queued (the row is kept and can be sent again) */
    public function handle(Subscriber $subscriber): bool
    {
        try {
            Mail::to($subscriber->email)->queue(new ConfirmSubscription($subscriber));
            $subscriber->forceFill(['confirmation_sent_at' => now()])->save();

            return true;
        } catch (Throwable $e) {
            // An unreachable queue must not turn a sign-up into an error page.
            report($e);

            return false;
        }
    }
}
