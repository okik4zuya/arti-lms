<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

/**
 * Recovery path for admin and support accounts, which do not use "Lupa password?" (they have MFA and wide
 * powers, so there is no email link to steal). Run on the server by someone who already has shell access.
 * The password is typed at a hidden prompt; it is never an argument (shell history) and never printed or logged.
 */
class ResetAdminPassword extends Command
{
    protected $signature = 'admin:reset-password {email : Email of the admin or support account}';

    protected $description = 'Set a new password for an admin or support account (hidden prompt, ends other sessions)';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if ($user === null || ! $user->hasAnyRole(['admin', 'support'])) {
            $this->error('No admin or support account with that email. Learners use "Lupa password?" on the login page.');

            return self::FAILURE;
        }

        $password = (string) $this->secret('New password (min 10 characters, letters and numbers)');
        $confirm = (string) $this->secret('Repeat the password');

        $validator = Validator::make(
            ['password' => $password, 'password_confirmation' => $confirm],
            ['password' => ['required', 'confirmed', Password::min(10)->letters()->numbers(), 'not_in:'.$user->email]],
        );

        if ($validator->fails()) {
            $this->error($validator->errors()->first('password'));

            return self::FAILURE;
        }

        $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
        // Other sessions end on their next request: the password hash changed (AuthenticateSession).

        Log::notice('admin password reset from the console', ['user_id' => $user->getKey()]);
        $this->info('Password updated for '.$user->email.'.');

        return self::SUCCESS;
    }
}
