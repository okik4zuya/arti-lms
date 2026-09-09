<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FilamentPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'learner']);
    }

    public function test_admin_can_access_filament_panel(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/jamrud')
            ->assertOk();
    }

    public function test_learner_is_blocked_from_filament_panel(): void
    {
        $learner = User::factory()->create();
        $learner->assignRole('learner');

        $this->actingAs($learner)
            ->get('/jamrud')
            ->assertForbidden();
    }
}
