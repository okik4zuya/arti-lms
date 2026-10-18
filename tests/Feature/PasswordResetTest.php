<?php

namespace Tests\Feature;

use App\Actions\Account\IssueSetPasswordLink;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private const SENT = 'Jika email itu terdaftar, tautan untuk mengatur password sudah dikirim.';

    private const STRONG = 'kata-sandi-baru-42';

    private function learner(array $over = []): User
    {
        return User::factory()->create($over + ['email' => 'buyer@example.test', 'password' => 'lama-sandi-1']);
    }

    private function staff(string $role): User
    {
        Role::findOrCreate($role);
        $user = User::factory()->create(['email' => "{$role}@example.test"]);
        $user->assignRole($role);

        return $user;
    }

    /** Asks for a link and returns the signed URL from the queued mail. */
    private function requestLink(string $email = 'buyer@example.test'): string
    {
        Mail::fake();
        $this->post('/lupa-sandi', ['email' => $email])->assertRedirect();
        $url = null;
        Mail::assertQueued(ResetPasswordMail::class, function (ResetPasswordMail $mail) use (&$url) {
            $url = $mail->url;

            return true;
        });

        return $url;
    }

    public function test_the_login_page_links_to_forgot_password_and_shows_a_status_line(): void
    {
        $this->get('/login')->assertOk()->assertSee('Lupa password?')->assertSee(route('password.request'), false);

        $this->withSession(['status' => 'Password sudah diatur.'])->get('/login')->assertSee('Password sudah diatur.');
        $this->get('/lupa-sandi')->assertOk()->assertSee('Kirim tautan');
    }

    public function test_a_known_email_gets_one_mail_and_the_answer_is_the_same_for_everyone(): void
    {
        Mail::fake();
        $this->learner();
        $this->staff('admin');
        $this->staff('support');

        $known = $this->post('/lupa-sandi', ['email' => 'buyer@example.test']);
        $unknown = $this->post('/lupa-sandi', ['email' => 'nobody@example.test']);
        $admin = $this->post('/lupa-sandi', ['email' => 'admin@example.test']);
        $support = $this->post('/lupa-sandi', ['email' => 'support@example.test']);

        foreach ([$known, $unknown, $admin, $support] as $response) {
            $response->assertRedirect()->assertSessionHas('status', fn ($s) => str_starts_with($s, self::SENT))->assertSessionHasNoErrors();
        }
        $this->assertSame($known->getSession()->get('status'), $unknown->getSession()->get('status'));
        $this->assertSame($known->getSession()->get('status'), $admin->getSession()->get('status'));

        Mail::assertQueued(ResetPasswordMail::class, 1);
        Mail::assertQueued(ResetPasswordMail::class, fn (ResetPasswordMail $m) => $m->hasTo('buyer@example.test'));
    }

    public function test_a_malformed_email_is_a_validation_error_not_a_lookup(): void
    {
        Mail::fake();

        $this->post('/lupa-sandi', ['email' => 'bukan-email'])->assertSessionHasErrors('email');
        $this->post('/lupa-sandi', [])->assertSessionHasErrors('email');
        Mail::assertNothingQueued();
    }

    public function test_the_mail_carries_a_signed_link_and_never_a_password(): void
    {
        $this->learner();
        $url = $this->requestLink();

        $mail = new ResetPasswordMail($url, 60);
        $html = $mail->render();

        $this->assertStringContainsString('Atur password', $html);
        $this->assertStringContainsString('60 menit', $html);
        $this->assertStringContainsString('hanya bisa dipakai satu kali', $html);
        $this->assertStringContainsString('b=users', $url);
        $this->assertStringContainsString('signature=', $url);
        $this->assertStringNotContainsString('lama-sandi-1', $html);
        $this->assertStringContainsString('ARTi tidak pernah meminta password', $html);
        // The link is also written out, for a mail client where the button does not work.
        $this->assertStringContainsString('Kalau tombol tidak berfungsi', $html);
        $this->assertStringContainsString('>'.htmlspecialchars($url).'</a>', $html);
    }

    public function test_the_whole_flow_sets_the_password_signs_the_old_one_out_and_burns_the_link(): void
    {
        $user = $this->learner();
        $url = $this->requestLink();

        $this->get($url)->assertOk()->assertSee('buyer@example.test')->assertSee('Pilih password baru');

        $this->post($url, ['password' => self::STRONG, 'password_confirmation' => self::STRONG])
            ->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $user->refresh();
        $this->assertTrue(Hash::check(self::STRONG, $user->password));
        $this->assertFalse(Hash::check('lama-sandi-1', $user->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'buyer@example.test']);

        $this->post('/login', ['email' => 'buyer@example.test', 'password' => self::STRONG])->assertRedirect(route('dashboard'));
    }

    public function test_the_old_password_stops_working_after_a_reset(): void
    {
        $this->learner();
        $url = $this->requestLink();
        $this->post($url, ['password' => self::STRONG, 'password_confirmation' => self::STRONG]);

        $this->post('/login', ['email' => 'buyer@example.test', 'password' => 'lama-sandi-1'])->assertSessionHasErrors('email');
    }

    public function test_a_link_used_once_is_dead(): void
    {
        $this->learner();
        $url = $this->requestLink();
        $this->post($url, ['password' => self::STRONG, 'password_confirmation' => self::STRONG])->assertRedirect(route('login'));

        $this->get($url)->assertOk()->assertSee('Tautan tidak berlaku');
        $this->post($url, ['password' => 'sandi-lain-diam-99', 'password_confirmation' => 'sandi-lain-diam-99'])
            ->assertRedirect(route('password.request'))
            ->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check(self::STRONG, User::first()->password));
    }

    public function test_a_browser_signed_in_as_someone_else_still_reaches_the_forgot_password_form(): void
    {
        $this->learner();
        $other = $this->learner(['email' => 'other@example.test']);

        $this->actingAs($other)->get('/lupa-sandi')->assertOk()->assertSee('Kirim tautan');
    }

    public function test_a_dead_link_tells_who_is_signed_in_or_offers_login(): void
    {
        $this->learner();
        $other = $this->learner(['email' => 'other@example.test']);
        $url = $this->requestLink();
        $this->post($url, ['password' => self::STRONG, 'password_confirmation' => self::STRONG]);

        $this->get($url)->assertSee('Masuk di sini')->assertDontSee('Keluar dari akun ini');
        $this->actingAs($other)->get($url)->assertSee('other@example.test')->assertSee('Keluar dari akun ini');
    }

    public function test_setting_a_password_signs_the_browser_out_so_the_login_page_shows(): void
    {
        $this->learner();
        $other = $this->learner(['email' => 'other@example.test']);
        $url = $this->requestLink();

        $this->actingAs($other)->post($url, ['password' => self::STRONG, 'password_confirmation' => self::STRONG])
            ->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_an_expired_link_is_refused(): void
    {
        $this->learner();
        $url = $this->requestLink();

        $this->travel(61)->minutes();

        $this->get($url)->assertForbidden();
        $this->post($url, ['password' => self::STRONG, 'password_confirmation' => self::STRONG])->assertForbidden();
        $this->assertTrue(Hash::check('lama-sandi-1', User::first()->password));
    }

    public function test_a_new_request_replaces_the_previous_link(): void
    {
        $this->learner();
        $first = $this->requestLink();
        $second = $this->requestLink();

        $this->get($first)->assertOk()->assertSee('Tautan tidak berlaku');
        $this->get($second)->assertOk()->assertSee('Pilih password baru');
    }

    public function test_a_tampered_link_is_refused(): void
    {
        $this->learner();
        $this->learner(['email' => 'other@example.test']);
        $url = $this->requestLink();

        $this->get(str_replace('b=users', 'b=welcome', $url))->assertForbidden();
        $this->get(str_replace('buyer%40example.test', 'other%40example.test', $url))->assertForbidden();
        $this->get(preg_replace('/signature=[^&]+/', 'signature=abc', $url))->assertForbidden();
        $this->get(route('password.reset', ['token' => 'x']))->assertForbidden();
    }

    public function test_password_rules_are_enforced_and_a_failed_attempt_keeps_the_link_alive(): void
    {
        $this->learner();
        $url = $this->requestLink();

        $this->post($url, ['password' => 'pendek1', 'password_confirmation' => 'pendek1'])->assertSessionHasErrors('password');
        $this->post($url, ['password' => 'hanyahurufpanjang', 'password_confirmation' => 'hanyahurufpanjang'])->assertSessionHasErrors('password');
        $this->post($url, ['password' => '12345678901234', 'password_confirmation' => '12345678901234'])->assertSessionHasErrors('password');
        $this->post($url, ['password' => self::STRONG, 'password_confirmation' => 'beda-sandi-42x'])->assertSessionHasErrors('password');
        $this->post($url, [])->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('lama-sandi-1', User::first()->password));
        $this->post($url, ['password' => self::STRONG, 'password_confirmation' => self::STRONG])->assertRedirect(route('login'));
    }

    public function test_a_weak_password_gets_one_message_and_the_form_states_the_rules_once(): void
    {
        $this->learner();
        $url = $this->requestLink();

        $this->get($url)->assertOk()->assertSee('Syarat password:')->assertDontSee('minlength');

        $response = $this->post($url, ['password' => 'pendek', 'password_confirmation' => 'pendek']);
        $response->assertSessionHasErrors('password');
        $this->assertSame(['Password belum memenuhi syarat.'], session('errors')->get('password'));
    }

    public function test_the_password_may_not_equal_the_email(): void
    {
        $this->learner(['email' => 'buyer12345@example.test']);
        $url = $this->requestLink('buyer12345@example.test');

        $this->post($url, ['password' => 'buyer12345@example.test', 'password_confirmation' => 'buyer12345@example.test'])
            ->assertSessionHasErrors('password');
    }

    public function test_the_first_link_of_a_purchase_has_no_time_limit_and_the_reset_link_does(): void
    {
        $user = $this->learner();
        $welcome = app(IssueSetPasswordLink::class)->handle($user, IssueSetPasswordLink::WELCOME);

        $this->assertStringContainsString('b=welcome', $welcome);
        $this->assertStringNotContainsString('expires=', $welcome);
        $this->get($welcome)->assertOk()->assertSee('Atur password akun belajar');

        $this->travel(400)->days();
        $this->get($welcome)->assertOk()->assertSee('Atur password akun belajar');
        $this->post($welcome, ['password' => self::STRONG, 'password_confirmation' => self::STRONG])->assertRedirect(route('login'));
        $this->get($welcome)->assertSee('Tautan tidak berlaku');

        // The "Lupa password?" link still expires with its signature.
        $reset = app(IssueSetPasswordLink::class)->handle($user->fresh());
        $this->assertStringContainsString('expires=', $reset);
    }

    public function test_the_first_link_dies_when_the_password_changes_by_any_other_route(): void
    {
        $user = $this->learner();
        $welcome = app(IssueSetPasswordLink::class)->handle($user, IssueSetPasswordLink::WELCOME);
        $this->get($welcome)->assertSee('Atur password akun belajar');

        // For example the buyer used "Lupa password?" first, or an admin changed the password.
        $user->forceFill(['password' => 'sandi-lain-77-xyz'])->save();

        $this->get($welcome)->assertSee('Tautan tidak berlaku');
        $this->post($welcome, ['password' => self::STRONG, 'password_confirmation' => self::STRONG])->assertRedirect(route('password.request'));
        $this->assertTrue(Hash::check('sandi-lain-77-xyz', $user->fresh()->password));
    }

    public function test_a_newer_first_link_replaces_the_older_one(): void
    {
        $user = $this->learner();
        $old = app(IssueSetPasswordLink::class)->handle($user, IssueSetPasswordLink::WELCOME);
        $new = app(IssueSetPasswordLink::class)->handle($user, IssueSetPasswordLink::WELCOME);

        $this->get($old)->assertSee('Tautan tidak berlaku');
        $this->get($new)->assertSee('Atur password akun belajar');
    }

    public function test_the_first_link_sets_the_password_once(): void
    {
        $user = $this->learner();
        $welcome = app(IssueSetPasswordLink::class)->handle($user, IssueSetPasswordLink::WELCOME);

        $this->post($welcome, ['password' => self::STRONG, 'password_confirmation' => self::STRONG])->assertRedirect(route('login'));
        $this->get($welcome)->assertSee('Tautan tidak berlaku');
    }

    public function test_staff_accounts_never_get_a_link(): void
    {
        $this->expectException(InvalidArgumentException::class);
        app(IssueSetPasswordLink::class)->handle($this->staff('admin'));
    }

    public function test_support_accounts_never_get_a_link(): void
    {
        $this->expectException(InvalidArgumentException::class);
        app(IssueSetPasswordLink::class)->handle($this->staff('support'), IssueSetPasswordLink::WELCOME);
    }

    public function test_a_learner_who_later_becomes_staff_cannot_use_an_old_link(): void
    {
        $user = $this->learner();
        $url = $this->requestLink();
        Role::findOrCreate('support');
        $user->assignRole('support');

        $this->get($url)->assertSee('Tautan tidak berlaku');
        $this->post($url, ['password' => self::STRONG, 'password_confirmation' => self::STRONG])->assertRedirect(route('password.request'));
        $this->assertTrue(Hash::check('lama-sandi-1', $user->fresh()->password));
    }

    public function test_the_remember_token_is_rotated_and_database_sessions_of_the_user_are_removed(): void
    {
        $user = $this->learner();
        $before = $user->remember_token;
        config(['session.driver' => 'database']);
        DB::table('sessions')->insert([
            ['id' => 'other-device', 'user_id' => $user->id, 'payload' => '', 'last_activity' => time()],
            ['id' => 'someone-else', 'user_id' => null, 'payload' => '', 'last_activity' => time()],
        ]);
        $url = $this->requestLink();

        $this->post($url, ['password' => self::STRONG, 'password_confirmation' => self::STRONG])->assertRedirect(route('login'));

        $this->assertNotSame($before, $user->fresh()->remember_token);
        $this->assertDatabaseMissing('sessions', ['id' => 'other-device']);
        $this->assertDatabaseHas('sessions', ['id' => 'someone-else']);
    }

    public function test_a_session_whose_password_changed_is_signed_out_on_its_next_request(): void
    {
        $user = $this->learner();

        $this->actingAs($user)->get('/app')->assertOk();
        $user->forceFill(['password' => self::STRONG])->save();

        $this->get('/app')->assertRedirect(route('login'));
    }

    public function test_requests_are_limited_per_address_and_per_email_with_separate_counters(): void
    {
        Mail::fake();

        for ($i = 1; $i <= 5; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => "10.0.0.{$i}"])->post('/lupa-sandi', ['email' => 'Buyer@Example.test'])->assertRedirect();
        }
        // Sixth try for the same address from a new IP: the per-email counter stops it.
        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.9'])->post('/lupa-sandi', ['email' => 'buyer@example.test'])->assertStatus(429);
        // Another address from that IP is fine: the counters are not shared.
        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.9'])->post('/lupa-sandi', ['email' => 'else@example.test'])->assertRedirect();

        // One IP is limited by its own counter after five requests a minute.
        for ($i = 1; $i <= 5; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => '10.9.9.9'])->post('/lupa-sandi', ['email' => "u{$i}@example.test"])->assertRedirect();
        }
        $this->withServerVariables(['REMOTE_ADDR' => '10.9.9.9'])->post('/lupa-sandi', ['email' => 'u6@example.test'])->assertStatus(429);

        // The login form has its own bucket and is untouched by the above.
        $this->withServerVariables(['REMOTE_ADDR' => '10.9.9.9'])->get('/login')->assertOk();
    }

    public function test_the_reset_form_has_its_own_limiter(): void
    {
        $this->learner();
        $url = $this->requestLink();

        for ($i = 1; $i <= 10; $i++) {
            $this->post($url, ['password' => 'x', 'password_confirmation' => 'x'])->assertSessionHasErrors('password');
        }
        $this->post($url, ['password' => self::STRONG, 'password_confirmation' => self::STRONG])->assertStatus(429);
    }
}
