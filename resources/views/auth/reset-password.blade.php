@extends('layouts.auth')

@section('title', $welcome ? 'Atur password' : 'Atur ulang password')

@section('content')
    @if ($invalid)
        <h1>Tautan tidak berlaku</h1>
        <p class="auth-lead">Tautan ini sudah dipakai, sudah kedaluwarsa, atau tidak lengkap. Minta tautan baru; yang lama otomatis tidak berlaku.</p>
        <p><a class="btn btn--primary btn--block" href="{{ route('password.request') }}">Minta tautan baru</a></p>
        @auth
            <p class="auth-lead">Browser ini sedang masuk sebagai <strong>{{ auth()->user()->email }}</strong>.</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn--outline btn--block">Keluar dari akun ini</button>
            </form>
        @else
            <p class="auth-lead">Sudah mengatur password? <a href="{{ route('login') }}">Masuk di sini</a>.</p>
        @endauth
    @else
        <h1>{{ $welcome ? 'Atur password akun belajar' : 'Pilih password baru' }}</h1>
        <p class="auth-lead">Akun: <strong>{{ $email }}</strong></p>

        {{-- No `minlength`: the browser would show its own English tooltip for the length alone, before the server checks the rest. --}}
        <form method="POST" action="{{ $action }}" class="stack">
            @csrf

            <div class="form-field">
                <label for="password" class="form-label">Password baru</label>
                <x-password-input id="password" name="password" autocomplete="new-password" :autofocus="true" :rules="true" />
                @error('password')
                    <span class="form-error" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-field">
                <label for="password_confirmation" class="form-label">Ulangi password</label>
                <x-password-input id="password_confirmation" name="password_confirmation" autocomplete="new-password" />
            </div>

            <button type="submit" class="btn btn--primary btn--block">Simpan password</button>
        </form>
    @endif
@endsection
