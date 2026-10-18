<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Recovery path for an admin or support account that lost its authenticator app and its recovery codes.
 * Clears the secret and the codes; with ADMIN_MFA_REQUIRED=true the person must set up MFA again at next login.
 */
class ResetAdminMfa extends Command
{
    protected $signature = 'admin:reset-mfa {email : Email of the admin or support account}';

    protected $description = 'Remove the authenticator secret and recovery codes of an admin or support account';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if ($user === null || ! $user->hasAnyRole(['admin', 'support'])) {
            $this->error('No admin or support account with that email.');

            return self::FAILURE;
        }

        $user->forceFill(['app_authentication_secret' => null, 'app_authentication_recovery_codes' => null])->save();

        Log::notice('admin MFA reset from the console', ['user_id' => $user->getKey()]);
        $this->info('MFA cleared for '.$user->email.'. They can set it up again at the next login.');

        return self::SUCCESS;
    }
}
