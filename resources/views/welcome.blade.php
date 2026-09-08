<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ARTi LMS</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell">
        <main class="main">
            <div class="topbar">
                <span class="topbar__title">ARTi LMS</span>
                @if (Route::has('login'))
                    <div class="topbar__user">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn--outline">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn--outline">Log in</a>
                        @endauth
                    </div>
                @endif
            </div>
            <div class="content text-center">
                <h1>ARTi LMS</h1>
                <p class="meta">Learning platform is under construction.</p>
            </div>
        </main>
    </div>
</body>
</html>
