<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMfaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('permissions:sync')->assertSuccessful();
    }

    private function userWith(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_the_user_model_speaks_the_filament_mfa_contracts(): void
    {
        $this->assertInstanceOf(HasAppAuthentication::class, new User);
        $this->assertInstanceOf(HasAppAuthenticationRecovery::class, new User);
    }

    public function test_the_secret_and_recovery_codes_are_encrypted_at_rest_and_never_serialised(): void
    {
        $user = $this->userWith('admin');
        $user->saveAppAuthenticationSecret('JBSWY3DPEHPK3PXP');
        $user->saveAppAuthenticationRecoveryCodes(['AAAA-1111', 'BBBB-2222']);

        $fresh = User::find($user->id);

        $this->assertSame('JBSWY3DPEHPK3PXP', $fresh->getAppAuthenticationSecret());
        $this->assertSame(['AAAA-1111', 'BBBB-2222'], $fresh->getAppAuthenticationRecoveryCodes());
        $this->assertSame($user->email, $fresh->getAppAuthenticationHolderName());

        $raw = (array) \DB::table('users')->where('id', $user->id)->first();
        $this->assertStringNotContainsString('JBSWY3DPEHPK3PXP', (string) $raw['app_authentication_secret']);
        $this->assertStringNotContainsString('AAAA-1111', (string) $raw['app_authentication_recovery_codes']);

        $this->assertStringNotContainsString('JBSWY3DPEHPK3PXP', $fresh->toJson());
        $this->assertStringNotContainsString('AAAA-1111', $fresh->toJson());
    }

    public function test_when_not_required_the_panel_opens_as_before(): void
    {
        $this->actingAs($this->userWith('admin'))->get('/jamrud/license-issuances')->assertOk();
    }

    public function test_the_requirement_is_off_by_default_while_mfa_is_postponed(): void
    {
        // Turned on with ADMIN_MFA_REQUIRED=true before the panel is used in production.
        $this->assertStringContainsString("env('ADMIN_MFA_REQUIRED', false)", file_get_contents(config_path('site.php')));
    }

    public function test_the_qr_code_library_is_installed(): void
    {
        $this->assertTrue(class_exists(\BaconQrCode\Writer::class));
    }

    public function test_a_learner_still_cannot_reach_the_panel_even_with_mfa_set_up(): void
    {
        $learner = $this->userWith('learner');
        $learner->saveAppAuthenticationSecret('JBSWY3DPEHPK3PXP');

        $this->actingAs($learner)->get('/jamrud')->assertForbidden();
    }
}
