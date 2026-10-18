<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminRecoveryCommandsTest extends TestCase
{
    use RefreshDatabase;

    private function staff(string $role = 'admin'): User
    {
        Role::findOrCreate($role);
        $user = User::factory()->create(['email' => 'boss@example.test', 'password' => 'lama-sandi-1']);
        $user->assignRole($role);

        return $user;
    }

    public function test_the_console_sets_a_new_admin_password_from_hidden_prompts(): void
    {
        $user = $this->staff();
        $before = $user->remember_token;
        Log::spy();

        $this->artisan('admin:reset-password boss@example.test')
            ->expectsQuestion('New password (min 10 characters, letters and numbers)', 'sandi-admin-baru-77')
            ->expectsQuestion('Repeat the password', 'sandi-admin-baru-77')
            ->expectsOutputToContain('Password updated')
            ->assertSuccessful();

        $user->refresh();
        $this->assertTrue(Hash::check('sandi-admin-baru-77', $user->password));
        $this->assertNotSame($before, $user->remember_token);
        Log::shouldHaveReceived('notice')->once()->withArgs(fn (string $m, array $c) => ! str_contains(json_encode($c), 'sandi'));
    }

    public function test_weak_or_mismatched_passwords_change_nothing(): void
    {
        $user = $this->staff('support');

        $this->artisan('admin:reset-password boss@example.test')
            ->expectsQuestion('New password (min 10 characters, letters and numbers)', 'pendek1')
            ->expectsQuestion('Repeat the password', 'pendek1')
            ->assertFailed();

        $this->artisan('admin:reset-password boss@example.test')
            ->expectsQuestion('New password (min 10 characters, letters and numbers)', 'sandi-admin-baru-77')
            ->expectsQuestion('Repeat the password', 'sandi-admin-beda-77')
            ->assertFailed();

        $this->assertTrue(Hash::check('lama-sandi-1', $user->fresh()->password));
    }

    public function test_learners_and_unknown_emails_are_refused_so_the_console_is_not_a_back_door_around_the_link(): void
    {
        Role::findOrCreate('learner');
        $learner = User::factory()->create(['email' => 'buyer@example.test', 'password' => 'lama-sandi-1']);
        $learner->assignRole('learner');

        $this->artisan('admin:reset-password buyer@example.test')->expectsOutputToContain('Lupa password')->assertFailed();
        $this->artisan('admin:reset-password nobody@example.test')->assertFailed();
        $this->artisan('admin:reset-mfa buyer@example.test')->assertFailed();

        $this->assertTrue(Hash::check('lama-sandi-1', $learner->fresh()->password));
    }

    public function test_the_mfa_reset_clears_the_secret_and_recovery_codes(): void
    {
        $user = $this->staff();
        $user->forceFill(['app_authentication_secret' => 'SECRET123', 'app_authentication_recovery_codes' => ['aaaa-bbbb', 'cccc-dddd']])->save();

        $this->artisan('admin:reset-mfa boss@example.test')->expectsOutputToContain('MFA cleared')->assertSuccessful();

        $user->refresh();
        $this->assertNull($user->app_authentication_secret);
        $this->assertNull($user->app_authentication_recovery_codes);
    }
}
