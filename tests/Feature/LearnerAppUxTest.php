<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\User;
use App\Support\CourseContent;
use App\Support\MarkdownRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class LearnerAppUxTest extends TestCase
{
    use RefreshDatabase;

    private const NEW = 'kata-sandi-baru-42';

    private function learnerWithCourse(): User
    {
        $user = User::factory()->create(['email' => 'buyer@example.test', 'password' => 'lama-sandi-1']);
        $course = Course::create(['slug' => 'arti-framework', 'title' => 'ARTi Framework']);
        CourseAccess::create(['user_id' => $user->id, 'course_id' => $course->id, 'granted_at' => now()]);

        return $user;
    }

    /** @return list<array{slug: string, title: string}> */
    private function subchapters(): array
    {
        $manifest = Yaml::parseFile(resource_path('content/arti-framework/manifest.yaml'));

        return collect($manifest['sections'])->flatMap(fn ($s) => $s['chapters'] ?? [])->flatMap(fn ($c) => $c['subchapters'] ?? [])->values()->all();
    }

    public function test_the_course_card_starts_continues_and_finishes(): void
    {
        $user = $this->learnerWithCourse();
        [$first, $second] = $this->subchapters();

        $this->actingAs($user)->get(route('dashboard'))->assertSee('Mulai belajar')->assertSee($first['title'])
            ->assertSee(route('content.show', ['course' => Course::first(), 'slug' => $first['slug']]), false);

        $this->actingAs($user)->post("/learn/arti-framework/{$first['slug']}/progress");

        $this->actingAs($user)->get(route('dashboard'))->assertSee('Lanjutkan belajar')->assertSee($second['title'])
            ->assertSee(route('content.show', ['course' => Course::first(), 'slug' => $second['slug']]), false);

        foreach ($this->subchapters() as $subchapter) {
            $this->actingAs($user)->post("/learn/arti-framework/{$subchapter['slug']}/progress");
        }

        $this->actingAs($user)->get(route('dashboard'))->assertSee('Selesai, baca lagi');
    }

    public function test_a_subchapter_links_to_its_neighbours(): void
    {
        $user = $this->learnerWithCourse();
        [$first, $second] = $this->subchapters();
        $course = Course::first();

        $this->actingAs($user)->get(route('content.show', ['course' => $course, 'slug' => $first['slug']]))
            ->assertOk()
            ->assertSee('Selanjutnya')
            ->assertSee(route('content.show', ['course' => $course, 'slug' => $second['slug']]), false)
            ->assertDontSee('Sebelumnya');

        $this->actingAs($user)->get(route('content.show', ['course' => $course, 'slug' => $second['slug']]))
            ->assertOk()
            ->assertSee('Sebelumnya')
            ->assertSee(route('content.show', ['course' => $course, 'slug' => $first['slug']]), false);
    }

    public function test_marking_complete_answers_json_without_a_redirect_and_the_form_still_works_without_it(): void
    {
        $user = $this->learnerWithCourse();
        [$first] = $this->subchapters();

        $this->actingAs($user)->postJson("/learn/arti-framework/{$first['slug']}/progress")
            ->assertOk()
            ->assertExactJson(['completed' => true, 'slug' => $first['slug']]);
        $this->assertSame(1, $user->progress()->whereNotNull('completed_at')->count());

        $this->actingAs($user)->post("/learn/arti-framework/{$first['slug']}/progress")->assertRedirect();
    }

    public function test_the_page_marks_the_progress_form_for_the_instant_script(): void
    {
        $user = $this->learnerWithCourse();
        [$first] = $this->subchapters();

        $this->actingAs($user)->get(route('content.show', ['course' => Course::first(), 'slug' => $first['slug']]))
            ->assertSee('data-progress-form', false)
            ->assertSee('data-slug="'.$first['slug'].'"', false);
    }

    public function test_a_rendered_page_is_cached_and_a_broken_cache_never_breaks_it(): void
    {
        $this->learnerWithCourse();
        $course = Course::first();
        [$first] = $this->subchapters();

        $this->partialMock(MarkdownRenderer::class, fn ($mock) => $mock->shouldReceive('toHtml')->twice()->passthru());

        $content = app(CourseContent::class);
        $one = $content->page($course, $first['slug']);
        $two = $content->page($course, $first['slug']);

        // One render for the two calls above; the third call (cache down) renders again, which is the second `toHtml`.
        $this->assertSame($one, $two);
        $this->assertNotEmpty($one['html']);

        Cache::shouldReceive('store')->andThrow(new \RuntimeException('redis is down'));
        $this->assertSame($one['html'], app(CourseContent::class)->page($course, $first['slug'])['html']);
    }
    public function test_the_empty_dashboard_names_the_account_and_explains_what_to_do(): void
    {
        $user = User::factory()->create(['email' => 'nobody@example.test']);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertSee('Belum ada course di akun ini')
            ->assertSee('nobody@example.test')
            ->assertSee('Hubungi kami');
    }

    public function test_after_setting_a_password_the_login_page_knows_the_email(): void
    {
        $user = $this->learnerWithCourse();
        $url = app(\App\Actions\Account\IssueSetPasswordLink::class)->handle($user, \App\Actions\Account\IssueSetPasswordLink::WELCOME);

        $this->post($url, ['password' => self::NEW, 'password_confirmation' => self::NEW])->assertRedirect(route('login'));

        $this->get('/login')->assertSee('value="buyer@example.test"', false)->assertSee('Password sudah diatur.');
    }

    public function test_password_fields_have_a_show_button_and_the_new_one_lists_its_rules(): void
    {
        $this->get('/login')->assertSee('data-password-toggle', false);

        $user = $this->learnerWithCourse();
        $this->actingAs($user)->get(route('password.change'))
            ->assertOk()
            ->assertSee('Syarat password:')
            ->assertSee('data-rule="length"', false)
            ->assertSee('data-rule="number"', false);
    }

    public function test_a_learner_can_change_their_own_password(): void
    {
        $user = $this->learnerWithCourse();

        $this->actingAs($user)->post(route('password.change'), [
            'current_password' => 'lama-sandi-1', 'password' => self::NEW, 'password_confirmation' => self::NEW,
        ])->assertRedirect(route('password.change'))->assertSessionHas('status');

        $this->assertTrue(Hash::check(self::NEW, $user->fresh()->password));
        $this->actingAs($user->fresh())->get(route('dashboard'))->assertOk();
    }

    public function test_changing_the_password_needs_the_current_one_and_a_strong_new_one(): void
    {
        $user = $this->learnerWithCourse();

        $this->actingAs($user)->post(route('password.change'), [
            'current_password' => 'salah-sandi-9', 'password' => self::NEW, 'password_confirmation' => self::NEW,
        ])->assertSessionHasErrors('current_password');

        $this->actingAs($user)->post(route('password.change'), [
            'current_password' => 'lama-sandi-1', 'password' => 'pendek', 'password_confirmation' => 'pendek',
        ])->assertSessionHasErrors('password');

        $this->actingAs($user)->post(route('password.change'), [
            'current_password' => 'lama-sandi-1', 'password' => 'lama-sandi-1', 'password_confirmation' => 'lama-sandi-1',
        ])->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('lama-sandi-1', $user->fresh()->password));
    }

    public function test_the_change_password_page_needs_a_signed_in_user(): void
    {
        $this->get(route('password.change'))->assertRedirect('/login');
        $this->post(route('password.change'), [])->assertRedirect('/login');
    }
}
