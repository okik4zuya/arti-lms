<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Filament attaches the "MFA required" middleware when the panel routes are registered (at boot), so the
 * switch has to be on before the application is created. phpunit.xml turns it off for the rest of the suite.
 */
class AdminMfaRequiredTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        $this->setMfaRequired('true');

        parent::setUp();

        $this->artisan('permissions:sync')->assertSuccessful();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->setMfaRequired('false');
    }

    private function setMfaRequired(string $value): void
    {
        putenv("ADMIN_MFA_REQUIRED={$value}");
        $_ENV['ADMIN_MFA_REQUIRED'] = $value;
        $_SERVER['ADMIN_MFA_REQUIRED'] = $value;
    }

    private function userWith(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_an_admin_without_mfa_is_sent_to_set_it_up_instead_of_the_panel(): void
    {
        $response = $this->actingAs($this->userWith('admin'))->get('/jamrud/license-issuances');

        $response->assertRedirect();
        $this->assertStringNotContainsString('license-issuances', (string) $response->headers->get('Location'));
    }

    public function test_support_is_held_to_the_same_rule(): void
    {
        $this->actingAs($this->userWith('support'))->get('/jamrud/license-issuances')->assertRedirect();
    }

    public function test_the_setup_page_that_the_redirect_points_to_opens_for_the_user(): void
    {
        $this->actingAs($admin = $this->userWith('admin'));

        $target = $this->get('/jamrud/license-issuances')->headers->get('Location');

        $this->actingAs($admin)->get($target)->assertOk();
    }

    public function test_an_admin_with_mfa_set_up_is_not_sent_to_the_setup_page(): void
    {
        $admin = $this->userWith('admin');
        $admin->saveAppAuthenticationSecret('JBSWY3DPEHPK3PXP');
        $admin->saveAppAuthenticationRecoveryCodes(['AAAA-1111']);

        $response = $this->actingAs($admin)->get('/jamrud/license-issuances');

        // Not the setup page: either the panel (session already challenged) or the code challenge.
        $location = (string) $response->headers->get('Location');
        $this->assertStringNotContainsString('set-up', $location);
    }

    public function test_a_learner_still_cannot_reach_the_panel(): void
    {
        $this->actingAs($this->userWith('learner'))->get('/jamrud')->assertForbidden();
    }
}
