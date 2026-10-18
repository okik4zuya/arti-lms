<?php

namespace App\Actions\Account;

use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use InvalidArgumentException;

/**
 * Builds the one-time link that lets an account holder choose a password. Nothing but the link leaves
 * this class: no password is generated, stored or logged. The token lives (hashed) in
 * `password_reset_tokens` and the broker deletes it when the password is set; creating a new link for a
 * user replaces the previous one.
 *
 * `reset` = the "Lupa password?" flow (60 minutes). `welcome` = the first link from a purchase: no time limit, valid
 * until the password is set or changed (the signature carries no expiry either).
 */
class IssueSetPasswordLink
{
    public const RESET = 'users';

    public const WELCOME = 'welcome';

    public function handle(User $user, string $broker = self::RESET): string
    {
        if ($user->hasAnyRole(['admin', 'support'])) {
            throw new InvalidArgumentException('Akun admin atau support tidak memakai tautan ini.');
        }

        if (! in_array($broker, [self::RESET, self::WELCOME], true)) {
            throw new InvalidArgumentException("Unknown password broker {$broker}.");
        }

        $token = Password::broker($broker)->createToken($user);
        $parameters = ['token' => $token, 'email' => $user->email, 'b' => $broker];

        // The first link has no expiry at all (not even in the signature); the "Lupa password?" link lasts 60 minutes.
        return $broker === self::WELCOME
            ? URL::signedRoute('password.reset', $parameters)
            : URL::temporarySignedRoute('password.reset', now()->addMinutes((int) config('auth.passwords.users.expire')), $parameters);
    }
}
