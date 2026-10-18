<?php

namespace App\Actions\Licensing;

use App\Actions\Account\IssueSetPasswordLink;
use App\Models\LicenseIssuance;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use InvalidArgumentException;

/**
 * Gives the buyer a one-time link to choose a new password for their learner account and returns it. No password
 * is generated, stored or logged here; the link is returned to the caller only and never put in the event detail.
 * The current password keeps working until the buyer sets a new one. Never usable on an admin or support account.
 */
class ResetAccountPassword
{
    public function __construct(private readonly IssueSetPasswordLink $links) {}

    /** @return string the signed one-time link (no time limit; dies when the password is set or changed) */
    public function handle(LicenseIssuance $row, User $actor): string
    {
        if (! $actor->can('account.reset_password')) {
            throw new AuthorizationException;
        }

        $account = $row->user;

        if ($account === null) {
            throw new InvalidArgumentException('Lisensi ini tidak punya akun belajar.');
        }

        if ($account->hasAnyRole(['admin', 'support'])) {
            throw new InvalidArgumentException('Akun admin atau support tidak bisa direset dari sini.');
        }

        $url = $this->links->handle($account, IssueSetPasswordLink::WELCOME);
        $row->logEvent('account_password_link', $actor->getKey());

        return $url;
    }
}
