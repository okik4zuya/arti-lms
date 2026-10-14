<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeds_an_admin_and_a_learner_who_can_log_in(): void
    {
        $this->seed();

        $admin = User::where('email', DatabaseSeeder::ADMIN_EMAIL)->firstOrFail();
        $learner = User::where('email', DatabaseSeeder::LEARNER_EMAIL)->firstOrFail();

        $this->assertTrue($admin->hasRole('admin'));
        $this->assertFalse($admin->hasRole('learner'));
        $this->assertTrue($learner->hasRole('learner'));
        $this->assertTrue($learner->courseAccess()->whereHas('course', fn ($q) => $q->where('slug', 'example-course'))->exists());

        $this->post('/login', ['email' => DatabaseSeeder::ADMIN_EMAIL, 'password' => DatabaseSeeder::PASSWORD])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($admin);

        $this->get('/jamrud')->assertOk();
    }

    public function test_learner_cannot_open_the_admin_panel(): void
    {
        $this->seed();

        $this->actingAs(User::where('email', DatabaseSeeder::LEARNER_EMAIL)->firstOrFail())
            ->get('/jamrud')
            ->assertForbidden();
    }

    public function test_seeding_twice_does_not_duplicate_or_fail(): void
    {
        $this->seed();
        $this->seed();

        $this->assertSame(2, User::count());
        $this->assertSame(1, Course::count());
    }
}
