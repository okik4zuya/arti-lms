<?php

namespace App\Actions\Licensing;

use App\Models\LicenseIssuance;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Sets a new random password on the buyer's learner account and returns it. The password is returned to the
 * caller only: it is not stored, logged or put in the event detail. Never usable on an admin or support account.
 */
class ResetAccountPassword
{
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

        $password = Str::password(12);

        $account->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
        $row->logEvent('account_password_reset', $actor->getKey());

        return $password;
    }
}
