<?php

namespace App\Http\Controllers;

use App\Rules\StrongPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/** A signed-in learner changes their own password (current, new, confirmation). Other devices are signed out. */
class AccountPasswordController extends Controller
{
    public function edit(): View
    {
        return view('auth.change-password');
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', new StrongPassword, 'not_in:'.$request->user()->email, 'different:current_password'],
        ], [
            'current_password.required' => 'Isi password saat ini.',
            'current_password.current_password' => 'Password saat ini salah.',
            'password.required' => 'Isi password baru.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'password.not_in' => 'Password tidak boleh sama dengan email.',
            'password.different' => 'Password baru harus berbeda dari yang sekarang.',
        ]);

        $new = (string) $request->input('password');
        $request->user()->forceFill(['password' => $new])->save();

        // Rotates the remember token and re-stamps this session with the new hash; every other session is dropped
        // on its next request by AuthenticateSession. (It wants the password the account has now, i.e. the new one.)
        Auth::logoutOtherDevices($new);

        return redirect()->route('password.change')->with('status', 'Password sudah diubah. Perangkat lain dikeluarkan.');
    }
}
