<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegacyRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function course(): Course
    {
        return Course::create(['slug' => 'example-course', 'title' => 'Example Course']);
    }

    public function test_login_page_renders_for_guests(): void
    {
        $this->get('/login')->assertOk()->assertSee('Log in');
    }

    public function test_login_redirects_to_the_dashboard_route(): void
    {
        $user = User::factory()->create(['password' => 'secret-pass']);

        $this->post('/login', ['email' => $user->email, 'password' => 'secret-pass'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_rejects_bad_credentials(): void
    {
        $user = User::factory()->create(['password' => 'secret-pass']);

        $this->from('/login')
            ->post('/login', ['email' => $user->email, 'password' => 'wrong'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_logout_ends_the_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout')->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get(route('dashboard'))->assertRedirect('/login');
    }

    public function test_dashboard_lists_only_courses_the_user_can_access(): void
    {
        $user = User::factory()->create();
        $course = $this->course();
        Course::create(['slug' => 'second-course', 'title' => 'Hidden Course']);
        CourseAccess::create(['user_id' => $user->id, 'course_id' => $course->id, 'granted_at' => now()]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Example Course')
            ->assertDontSee('Hidden Course');
    }

    public function test_learn_route_requires_authentication(): void
    {
        $this->course();

        $this->get('/learn/example-course/01-intro')->assertRedirect('/login');
    }

    public function test_learn_route_forbids_users_without_access(): void
    {
        $this->course();

        $this->actingAs(User::factory()->create())
            ->get('/learn/example-course/01-intro')
            ->assertForbidden();
    }

    public function test_learn_route_renders_markdown_for_users_with_access(): void
    {
        $user = User::factory()->create();
        $course = $this->course();
        CourseAccess::create(['user_id' => $user->id, 'course_id' => $course->id, 'granted_at' => now()]);

        $this->actingAs($user)
            ->get('/learn/example-course/01-intro')
            ->assertOk()
            ->assertSee('Introduction');
    }

    public function test_learn_route_404s_for_unknown_subchapter_and_image(): void
    {
        $user = User::factory()->create();
        $course = $this->course();
        CourseAccess::create(['user_id' => $user->id, 'course_id' => $course->id, 'granted_at' => now()]);

        $this->actingAs($user)->get('/learn/example-course/nope')->assertNotFound();
        $this->actingAs($user)->get('/learn/example-course/01-intro/images/nope.png')->assertNotFound();
    }

    public function test_progress_can_be_stored_for_a_subchapter(): void
    {
        $user = User::factory()->create();
        $course = $this->course();
        CourseAccess::create(['user_id' => $user->id, 'course_id' => $course->id, 'granted_at' => now()]);

        $this->actingAs($user)
            ->post('/learn/example-course/01-intro/progress')
            ->assertRedirect();

        $this->assertDatabaseHas('progress', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'subchapter_slug' => '01-intro',
        ]);
    }
}
