@extends('layouts.auth')

@section('title', 'Lupa password')

@section('content')
    <h1>Lupa password?</h1>
    <p class="auth-lead">Masukkan email akun belajar Anda. Kami kirim tautan untuk memilih password baru.</p>

    @if (session('status'))
        <p class="form-status" role="status">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="stack">
        @csrf

        <div class="form-field">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-input" required autofocus autocomplete="email">
            @error('email')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn--primary btn--block">Kirim tautan</button>
    </form>

    <p class="auth-links"><a href="{{ route('login') }}">Kembali ke halaman masuk</a></p>
@endsection
