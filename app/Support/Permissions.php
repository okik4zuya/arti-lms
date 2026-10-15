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
    ];

    public const ADMIN_ONLY = [
        'payment.verify',
        'license.revoke',
        'license.delete_pending',
        'license.edit_amount',
        'site.manage',
        'course.manage',
        'user.manage',
    ];

    /** @return list<string> */
    public static function all(): array
    {
        return [...self::SUPPORT, ...self::ADMIN_ONLY];
    }
}
