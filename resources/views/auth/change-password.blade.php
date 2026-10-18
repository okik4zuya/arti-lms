@extends('layouts.auth')

@section('title', 'Ubah password')

@section('content')
    <h1>Ubah password</h1>
    <p class="auth-lead">Akun: <strong>{{ auth()->user()->email }}</strong>. Perangkat lain akan dikeluarkan setelah password diubah.</p>

    @if (session('status'))
        <p class="form-status" role="status">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('password.change') }}" class="stack">
        @csrf

        <div class="form-field">
            <label for="current_password" class="form-label">Password saat ini</label>
            <x-password-input id="current_password" name="current_password" autocomplete="current-password" :autofocus="true" />
            @error('current_password')
                <span class="form-error" role="alert">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-field">
            <label for="password" class="form-label">Password baru</label>
            <x-password-input id="password" name="password" autocomplete="new-password" :rules="true" />
            @error('password')
                <span class="form-error" role="alert">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-field">
            <label for="password_confirmation" class="form-label">Ulangi password baru</label>
            <x-password-input id="password_confirmation" name="password_confirmation" autocomplete="new-password" />
        </div>

        <button type="submit" class="btn btn--primary btn--block">Simpan password</button>
    </form>

    <p class="auth-links"><a href="{{ route('dashboard') }}">Kembali ke course</a></p>
@endsection
