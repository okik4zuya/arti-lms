<?php

namespace App\Http\Controllers;

use App\Actions\Account\IssueSetPasswordLink;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * "Lupa password?" and the page where the one-time link lands (also used for the first link of a purchase).
 *
 * - The request form answers the same sentence for every address, so it cannot be used to find out who is
 *   registered. Admin and support accounts get no mail at all: they recover through the server.
 * - The link is a signed URL carrying the token, the email and the broker name; the form posts back to that
 *   same signed URL, so the 60-minute / 48-hour choice cannot be edited.
 */
class PasswordResetController extends Controller
{
    private const SENT = 'Jika email itu terdaftar, tautan untuk mengatur password sudah dikirim. Cek kotak masuk dan folder spam; tautannya berlaku 60 menit.';

    public function request(): View
    {
        return view('auth.forgot-password');
    }

    public function send(Request $request, IssueSetPasswordLink $link): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email']], [
            'email.required' => 'Isi alamat email Anda.',
            'email.email' => 'Alamat email tidak valid.',
        ]);

        $user = User::where('email', $data['email'])->first();

        if ($user !== null && ! $user->hasAnyRole(['admin', 'support'])) {
            Mail::to($user->email)->queue(new ResetPasswordMail(
                $link->handle($user, IssueSetPasswordLink::RESET),
                (int) config('auth.passwords.users.expire'),
            ));
        }

        return back()->with('status', self::SENT);
    }

    public function show(Request $request, string $token): View
    {
        $user = $this->usableUser($request, $token);

        return view('auth.reset-password', [
            'invalid' => $user === null,
            'welcome' => $request->query('b') === IssueSetPasswordLink::WELCOME,
            'email' => $request->query('email'),
            'action' => $request->fullUrl(),
        ]);
    }

    public function update(Request $request, string $token): RedirectResponse
    {
        $user = $this->usableUser($request, $token);

        if ($user === null) {
            return redirect()->route('password.request')->withErrors(['email' => 'Tautan tidak berlaku lagi. Minta tautan baru di bawah.']);
        }

        // The requirements are stated once, as a hint above the form. One closure checks all three together, so
        // the buyer never gets them one at a time (Laravel's Password rule reports the length first, the rest later).
        $request->validate([
            'password' => [
                'required', 'confirmed',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (mb_strlen((string) $value) < 10 || ! preg_match('/\pL/u', (string) $value) || ! preg_match('/\pN/u', (string) $value)) {
                        $fail('Password belum memenuhi syarat di atas.');
                    }
                },
                'not_in:'.$user->email,
            ],
        ], [
            'password.required' => 'Isi password baru.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'password.not_in' => 'Password tidak boleh sama dengan email.',
        ]);

        $status = Password::broker($request->query('b'))->reset(
            ['email' => $user->email, 'password' => $request->input('password'), 'token' => $token],
            function (User $account, string $password) {
                $account->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
                // Other sessions on the database driver are removed outright; on any driver the
                // AuthenticateSession middleware drops them on their next request, because the hash changed.
                if (config('session.driver') === 'database') {
                    DB::table(config('session.table', 'sessions'))->where('user_id', $account->getKey())->delete();
                }
                event(new PasswordReset($account));
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            return redirect()->route('password.request')->withErrors(['email' => 'Tautan tidak berlaku lagi. Minta tautan baru di bawah.']);
        }

        return redirect()->route('login')->with('status', 'Password sudah diatur. Silakan masuk dengan password baru.');
    }

    /** The account the link is for, or null when the token is wrong, used, expired, or the account may not use links. */
    private function usableUser(Request $request, string $token): ?User
    {
        $broker = $request->query('b');
        $email = $request->query('email');

        if (! in_array($broker, [IssueSetPasswordLink::RESET, IssueSetPasswordLink::WELCOME], true) || ! is_string($email)) {
            return null;
        }

        $user = User::where('email', $email)->first();

        if ($user === null || $user->hasAnyRole(['admin', 'support'])) {
            return null;
        }

        return Password::broker($broker)->tokenExists($user, $token) ? $user : null;
    }
}
