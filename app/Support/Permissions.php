<?php

namespace App\Support;

/**
 * The permission matrix of the admin panel. `admin` gets everything through Gate::before; `support`
 * gets the SUPPORT list. `learner` has no panel access. Synced by `php artisan permissions:sync`.
 */
final class Permissions
{
    public const SUPPORT = [
        'license.view',
        'license.view_amount',
        'license.sync',
        'license.issue',
        'license.reveal_key',
        'license.edit_contact',
        'license.release_device',
        'license.extend',
        'license.rotate_key',
        'payment.attach',
        'account.reset_password',
        // Orders from the payment gateway: support can look and re-send the purchase mail, not refund.
        'order.view',
        'order.resend',
    ];

    public const ADMIN_ONLY = [
        'payment.verify',
        'license.revoke',
        'license.delete_pending',
        'license.edit_amount',
        'site.manage',
        'course.manage',
        'user.manage',
        // The mailing list holds personal data and is used for marketing, which support staff do not do.
        'subscriber.manage',
        // Money: refunds and orders parked for a decision.
        'order.refund',
        'order.resolve',
    ];

    /** @return list<string> */
    public static function all(): array
    {
        return [...self::SUPPORT, ...self::ADMIN_ONLY];
    }
}
