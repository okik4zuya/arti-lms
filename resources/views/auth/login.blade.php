<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in — ARTi LMS</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell">
        <main class="main">
            <div class="topbar">
                <span class="topbar__title">ARTi LMS</span>
            </div>
            <div class="content auth-content">
                <div class="card auth-card">
                    <div class="card__body">
                        <h1>Log in</h1>

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
                                <label for="password" class="form-label">Password</label>
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
                                    Remember me
                                </label>
                            </div>

                            <button type="submit" class="btn btn--primary btn--block">Log in</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
