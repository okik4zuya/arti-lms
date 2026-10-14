<?php

namespace Tests\Feature;

use App\Filament\Resources\CourseResource\Pages\CreateCourse;
use App\Filament\Resources\CourseResource\Pages\EditCourse;
use App\Filament\Resources\CourseResource\Pages\ListCourses;
use App\Filament\Resources\CourseResource\RelationManagers\AccessRelationManager;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FilamentCourseResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'learner']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin);
        Filament::setCurrentPanel('jamrud');
    }

    public function test_course_pages_render_for_admin(): void
    {
        $course = Course::create(['slug' => 'example-course', 'title' => 'Example Course']);

        $this->get('/jamrud/courses')->assertOk()->assertSee('example-course');
        $this->get('/jamrud/courses/create')->assertOk();
        $this->get("/jamrud/courses/{$course->slug}/edit")->assertOk()->assertSee('Example Course');
    }

    public function test_courses_are_listed_in_the_table(): void
    {
        $courses = collect([
            Course::create(['slug' => 'one', 'title' => 'One']),
            Course::create(['slug' => 'two', 'title' => 'Two']),
        ]);

        Livewire::test(ListCourses::class)->assertCanSeeTableRecords($courses);
    }

    public function test_course_can_be_created_with_unique_slug(): void
    {
        Livewire::test(CreateCourse::class)
            ->fillForm(['slug' => 'new-course', 'title' => 'New Course'])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('courses', ['slug' => 'new-course']);

        Livewire::test(CreateCourse::class)
            ->fillForm(['slug' => 'new-course', 'title' => 'Again'])
            ->call('create')
            ->assertHasFormErrors(['slug']);
    }

    public function test_admin_can_create_a_learner_and_grant_access(): void
    {
        $course = Course::create(['slug' => 'example-course', 'title' => 'Example Course']);

        Livewire::test(AccessRelationManager::class, [
            'ownerRecord' => $course,
            'pageClass' => EditCourse::class,
        ])
            ->callAction(TestAction::make('createLearner')->table(), [
                'name' => 'New Learner',
                'email' => 'learner@example.test',
                'password' => 'temp-pass-123',
            ])
            ->assertHasNoFormErrors();

        $learner = User::where('email', 'learner@example.test')->firstOrFail();
        $this->assertTrue($learner->hasRole('learner'));
        $this->assertDatabaseHas('course_access', ['course_id' => $course->id, 'user_id' => $learner->id]);
    }

    public function test_admin_can_grant_access_to_an_existing_learner_and_revoke_it(): void
    {
        $course = Course::create(['slug' => 'example-course', 'title' => 'Example Course']);
        $learner = User::factory()->create();
        $learner->assignRole('learner');

        $component = Livewire::test(AccessRelationManager::class, [
            'ownerRecord' => $course,
            'pageClass' => EditCourse::class,
        ])
            ->callAction(TestAction::make('grantAccess')->table(), ['user_id' => $learner->id])
            ->assertHasNoFormErrors();

        $access = CourseAccess::where('course_id', $course->id)->where('user_id', $learner->id)->firstOrFail();
        $this->assertNotNull($access->granted_at);

        $component->callAction(TestAction::make('delete')->table($access));

        $this->assertDatabaseMissing('course_access', ['id' => $access->id]);
    }
}
