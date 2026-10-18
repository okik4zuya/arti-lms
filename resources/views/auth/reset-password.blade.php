@extends('layouts.auth')

@section('title', $welcome ? 'Atur password' : 'Atur ulang password')

@section('content')
    @if ($invalid)
        <h1>Tautan tidak berlaku</h1>
        <p class="auth-lead">Tautan ini sudah dipakai, sudah kedaluwarsa, atau tidak lengkap. Minta tautan baru; yang lama otomatis tidak berlaku.</p>
        <p><a class="btn btn--primary btn--block" href="{{ route('password.request') }}">Minta tautan baru</a></p>
    @else
        <h1>{{ $welcome ? 'Atur password akun belajar' : 'Pilih password baru' }}</h1>
        <p class="auth-lead">Akun: <strong>{{ $email }}</strong></p>
        <p class="auth-lead"><strong>Syarat password:</strong> minimal 10 karakter, memuat huruf dan angka.</p>

        {{-- No `minlength`: the browser would show its own English tooltip for the length alone, before the server checks the rest. --}}
        <form method="POST" action="{{ $action }}" class="stack">
            @csrf

            <div class="form-field">
                <label for="password" class="form-label">Password baru</label>
                <input id="password" type="password" name="password" class="form-input" required autofocus autocomplete="new-password">
                @error('password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-field">
                <label for="password_confirmation" class="form-label">Ulangi password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-input" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn--primary btn--block">Simpan password</button>
        </form>
    @endif
@endsection
