<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — ARTi LMS</title>
    <meta name="robots" content="noindex">

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">


    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="auth-page">
        <div class="auth-brand">
            <img src="{{ asset('images/logo.png') }}" alt="ARTi LMS" class="auth-brand__logo">
            <span class="auth-brand__name">ARTi LMS</span>
        </div>
        <div class="card auth-card">
            <div class="card__body">
                <h1>Masuk</h1>

                @if (session('status'))
                    <p class="form-status" role="status">{{ session('status') }}</p>
                @endif

                <form method="POST" action="{{ route('login') }}" class="stack">
                    @csrf

                    <div class="form-field">
                        <label for="email" class="form-label">Email</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="form-input"
                            required
                            autofocus
                            autocomplete="email"
                        >
                        @error('email')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="password" class="form-label">Kata sandi</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="form-input"
                            required
                            autocomplete="current-password"
                        >
                        @error('password')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-field form-field--checkbox">
                        <label for="remember" class="form-label">
                            <input id="remember" type="checkbox" name="remember">
                            Ingat saya
                        </label>
                    </div>

                    <button type="submit" class="btn btn--primary btn--block">Masuk</button>
                </form>

                <p class="auth-links"><a href="{{ route('password.request') }}">Lupa password?</a></p>
            </div>
        </div>
    </div>
</body>
</html>
