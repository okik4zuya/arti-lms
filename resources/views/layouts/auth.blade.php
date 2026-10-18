<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — ARTi LMS</title>
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
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
