<?php

namespace Tests\Feature;

use App\Filament\Resources\LicenseIssuanceResource\Pages\ListLicenseIssuances;
use App\Models\LicenseIssuance;
use App\Models\User;
use App\Support\Permissions;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LicenseRolesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('permissions:sync')->assertSuccessful();
        config(['services.licensing.url' => 'https://worker.test/v1', 'services.licensing.admin_token' => 'tok']);
        Filament::setCurrentPanel('jamrud');
    }

    private function userWith(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function row(): LicenseIssuance
    {
        $row = new LicenseIssuance(['order_ref' => 'WA-1', 'email' => 'buyer@example.test', 'name' => 'Buyer', 'tier' => 'individual', 'months' => 12]);
        $row->forceFill(['key_id' => 'k1', 'key' => 'ARTI-ABCD-EFGH-JKMN-PQRS', 'status' => LicenseIssuance::ACTIVE, 'seats' => 1, 'expires_at' => now()->addYear()])->save();

        return $row;
    }

    public function test_the_sync_command_is_idempotent_and_builds_the_matrix(): void
    {
        $this->artisan('permissions:sync')->assertSuccessful();

        $this->assertSame(count(Permissions::all()), Permission::count());
        $this->assertSame(3, Role::count());
        $this->assertEqualsCanonicalizing(Permissions::SUPPORT, Role::findByName('support')->permissions->pluck('name')->all());
        $this->assertEqualsCanonicalizing(Permissions::all(), Role::findByName('admin')->permissions->pluck('name')->all());
        $this->assertCount(0, Role::findByName('learner')->permissions);
    }

    public function test_support_has_exactly_its_permissions_and_admin_has_everything(): void
    {
        $support = $this->userWith('support');
        $admin = $this->userWith('admin');

        foreach (Permissions::SUPPORT as $ability) {
            $this->assertTrue($support->can($ability), $ability);
        }
        foreach (Permissions::ADMIN_ONLY as $ability) {
            $this->assertFalse($support->can($ability), $ability);
            $this->assertTrue($admin->can($ability), $ability);
        }
        $this->assertFalse($support->can('something.unknown'));
    }

    public function test_support_enters_the_panel_but_not_courses_or_settings(): void
    {
        $support = $this->userWith('support');

        $this->actingAs($support)->get('/jamrud')->assertOk();
        $this->actingAs($support)->get('/jamrud/license-issuances')->assertOk();
        $this->actingAs($support)->get('/jamrud/courses')->assertForbidden();
        $this->actingAs($support)->get('/jamrud/courses/create')->assertForbidden();
        $this->actingAs($support)->get('/jamrud/site-settings')->assertForbidden();
    }

    public function test_admin_still_reaches_courses_and_settings(): void
    {
        $admin = $this->userWith('admin');

        $this->actingAs($admin)->get('/jamrud/courses')->assertOk();
        $this->actingAs($admin)->get('/jamrud/site-settings')->assertOk();
    }

    public function test_a_learner_is_blocked_from_the_panel(): void
    {
        $this->actingAs($this->userWith('learner'))->get('/jamrud')->assertForbidden();
    }

    public function test_support_sees_the_license_actions(): void
    {
        $row = $this->row();

        Livewire::actingAs($this->userWith('support'))->test(ListLicenseIssuances::class)
            ->assertActionVisible('issue')
            ->assertActionVisible(TestAction::make('sendKey')->table($row))
            ->assertActionVisible(TestAction::make('editContact')->table($row))
            ->assertActionVisible(TestAction::make('history')->table($row))
            ->assertActionVisible(TestAction::make('sync')->table($row));
    }

    public function test_a_view_only_user_has_no_action_buttons_and_nothing_is_logged(): void
    {
        $row = $this->row();
        $viewer = User::factory()->create();
        $viewer->givePermissionTo('license.view');

        Livewire::actingAs($viewer)->test(ListLicenseIssuances::class)
            ->assertActionHidden('issue')
            ->assertActionHidden(TestAction::make('sendKey')->table($row))
            ->assertActionHidden(TestAction::make('editContact')->table($row))
            ->assertActionHidden(TestAction::make('sync')->table($row));

        $this->assertDatabaseMissing('license_issuance_events', ['event' => 'key_revealed']);
    }

    public function test_the_amount_column_is_for_permission_holders_only(): void
    {
        $this->row();
        $viewer = User::factory()->create();
        $viewer->givePermissionTo('license.view');

        Livewire::actingAs($viewer)->test(ListLicenseIssuances::class)->assertTableColumnHidden('amount_idr');
    }
}
