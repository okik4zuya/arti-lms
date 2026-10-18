<?php

namespace App\Actions\Orders;

use App\Actions\Licensing\RevokeLicense;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\LicenseIssuance;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use InvalidArgumentException;

/**
 * The refund mechanism that stays in the system although the landing page no longer promises one.
 * Admin only. It does the parts the system owns: revoke the license at the Worker, remove course access
 * (unless the buyer holds another active license), and mark the order. The money itself is returned in the
 * gateway dashboard; this class never calls the gateway.
 *
 * The Worker call comes first: if it fails the order is left as it was and the admin can try again.
 */
class RefundOrder
{
    public function __construct(private readonly RevokeLicense $revoke) {}

    public function handle(Order $order, User $actor, string $reason, string $confirmEmail): Order
    {
        if (! $actor->can('order.refund')) {
            throw new AuthorizationException;
        }

        if (! in_array($order->status, [Order::PAID, Order::FULFILLING, Order::FULFILLED, Order::NEEDS_ATTENTION], true)) {
            throw new InvalidArgumentException('Pesanan ini belum dibayar atau sudah dikembalikan.');
        }

        if (strcasecmp(trim($confirmEmail), $order->email) !== 0) {
            throw new InvalidArgumentException('Email konfirmasi tidak sama dengan email pesanan.');
        }

        $reason = trim($reason);
        if ($reason === '') {
            throw new InvalidArgumentException('Alasan wajib diisi.');
        }

        $license = $order->licenseIssuance;

        if ($license !== null && $license->key_id !== null && $license->status !== LicenseIssuance::REVOKED) {
            $this->revoke->handle($license, $actor, 'Refund', $reason, $order->email);
        }

        $this->removeCourseAccess($order);

        $order->transition(
            [Order::PAID, Order::FULFILLING, Order::FULFILLED, Order::NEEDS_ATTENTION],
            Order::REFUNDED,
            ['refunded_at' => now(), 'refund_reason' => mb_substr($reason, 0, 250)],
        );
        $order->logEvent('refunded', mb_substr($reason, 0, 200), $actor->getKey());

        return $order;
    }

    private function removeCourseAccess(Order $order): void
    {
        if ($order->user_id === null) {
            return;
        }

        $stillLicensed = LicenseIssuance::where('email', $order->email)
            ->where('status', LicenseIssuance::ACTIVE)
            ->where('id', '!=', $order->license_issuance_id)
            ->exists();

        if ($stillLicensed) {
            return;
        }

        $course = Course::where('slug', config('site.offer.course_slug'))->first();

        if ($course !== null) {
            CourseAccess::where('user_id', $order->user_id)->where('course_id', $course->id)->delete();
        }
    }
}
