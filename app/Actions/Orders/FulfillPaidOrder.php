<?php

namespace App\Actions\Orders;

use App\Actions\Account\IssueSetPasswordLink;
use App\Actions\Learners\CreateLearner;
use App\Actions\Licensing\IssueLicense;
use App\Actions\Licensing\IssueRequest;
use App\Jobs\SendOrderWhatsApp;
use App\Mail\PurchaseMail;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\LicenseIssuance;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

/**
 * Paid order -> learner account -> license -> email. Every stage records what it did on the order, so a retry
 * (after a Worker outage, a crash, a duplicate job) continues where it stopped and never repeats a finished
 * stage: the account is found by email, the license is issued under the order's public id (the Worker is
 * idempotent on it), and the mail is claimed with a single compare-and-set on `email_sent_at`.
 *
 * No password exists anywhere in this flow: the account is created with a random one nobody sees, and the
 * buyer chooses theirs through the one-time link.
 */
class FulfillPaidOrder
{
    public function __construct(
        private readonly CreateLearner $createLearner,
        private readonly IssueLicense $issueLicense,
        private readonly IssueSetPasswordLink $links,
    ) {}

    public function handle(Order $order): void
    {
        if (! in_array($order->status, [Order::PAID, Order::FULFILLING], true)) {
            return;
        }

        $order->transition([Order::PAID, Order::FULFILLING], Order::FULFILLING);

        $user = $this->learner($order);
        if ($user === null) {
            return;
        }

        $license = $this->license($order, $user);
        if ($license === null) {
            return;
        }

        $this->deliver($order, $user);

        if ($order->transition([Order::FULFILLING], Order::FULFILLED, ['fulfilled_at' => now()])) {
            $order->logEvent('fulfilled');
        }
    }

    private function learner(Order $order): ?User
    {
        if ($order->user_id !== null) {
            return $order->user;
        }

        $user = User::where('email', $order->email)->first();

        if ($user !== null && $user->hasAnyRole(['admin', 'support'])) {
            $order->flagAttention('staff_email', 'buyer email belongs to a staff account');

            return null;
        }

        if ($user === null) {
            $user = $this->createLearner->handle($order->name, $order->email, Str::random(64));
            $order->logEvent('learner_created');
        }

        $this->grantCourse($order, $user);
        $order->forceFill(['user_id' => $user->id])->save();

        return $user;
    }

    private function grantCourse(Order $order, User $user): void
    {
        $course = Course::where('slug', config('site.offer.course_slug'))->first();

        if ($course === null) {
            // The sale still goes through; an admin grants access once the course exists.
            $order->forceFill(['attention_note' => 'course_missing'])->save();
            $order->logEvent('course_missing', (string) config('site.offer.course_slug'));

            return;
        }

        CourseAccess::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['granted_at' => now()],
        );
    }

    private function license(Order $order, User $user): ?LicenseIssuance
    {
        if ($order->license_issuance_id !== null) {
            return $order->licenseIssuance;
        }

        $already = $order->attention_note !== 'approved' && LicenseIssuance::where('email', $order->email)
            ->where('order_ref', '!=', $order->public_id)
            ->where('status', LicenseIssuance::ACTIVE)
            ->exists();

        if ($already) {
            $order->flagAttention('duplicate_purchase', 'an active license already exists for this email');

            return null;
        }

        $license = $this->issueLicense->handle(new IssueRequest(
            email: $order->email,
            name: $order->name,
            months: (int) config('site.offer.license_months'),
            orderRef: $order->public_id,
            amountIdr: $order->amount,
            source: $order->gateway,
            buyerWhatsapp: $order->whatsapp,
            userId: $user->id,
            paymentRef: $order->gateway_ref,
        ));

        $order->forceFill(['license_issuance_id' => $license->id])->save();
        $order->logEvent('license_issued');

        return $license;
    }

    /** Queue the purchase mail once. A new account gets the first set-password link; an old one does not need it. */
    private function deliver(Order $order, User $user): void
    {
        $claimed = Order::whereKey($order->id)->whereNull('email_sent_at')->update(['email_sent_at' => now()]) === 1;

        if (! $claimed) {
            return;
        }

        try {
            $url = $order->events()->where('event', 'learner_created')->exists()
                ? $this->links->handle($user, IssueSetPasswordLink::WELCOME)
                : null;

            Mail::to($order->email)->queue(new PurchaseMail($order->id, $url));
        } catch (Throwable $e) {
            Order::whereKey($order->id)->update(['email_sent_at' => null]);

            throw $e;
        }

        $order->logEvent('email_queued');

        // A separate, optional stage: whatever happens to it, the order stays fulfilled.
        if (filled($order->whatsapp)) {
            try {
                SendOrderWhatsApp::dispatch($order->id, $url);
            } catch (Throwable) {
                $order->logEvent('whatsapp_dispatch_failed');
            }
        }
    }
}
