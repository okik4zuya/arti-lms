<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ARTi') — ARTi Framework</title>
    <meta name="description" content="@yield('description', 'ARTi Framework: sistem kerja riset berbasis Claude Code dengan memori antar sesi dan rujukan yang bisa dilacak.')">
    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

    <meta property="og:site_name" content="ARTi Framework">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('title', 'ARTi')">
    <meta property="og:description" content="@yield('description', 'ARTi Framework: sistem kerja riset berbasis Claude Code dengan memori antar sesi dan rujukan yang bisa dilacak.')">
    <meta property="og:url" content="{{ $canonical ?? url()->current() }}">
    <meta property="og:image" content="{{ asset('images/landing/og-image.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="id_ID">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="alternate" type="application/atom+xml" title="Artikel ARTi" href="{{ route('feed') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/site.css'])
    @stack('head')
</head>
<body>
    <header class="site-header">
        <div class="container site-header__inner">
            <a href="{{ route('home') }}" class="site-brand" aria-label="ARTi — beranda">
                <img src="{{ asset('images/wordmark.png') }}" alt="ARTi" class="site-brand__logo">
            </a>

            <div class="site-header__right">
                <nav class="site-nav site-nav--desktop" aria-label="Utama">
                    @include('layouts.partials.site-links')
                </nav>

                @yield('header_cta')

                <span class="site-header__account">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn--outline">Buka aplikasi</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn--outline">Masuk</a>
                    @endauth
                </span>

                <details class="nav-menu">
                    <summary aria-label="Menu">
                        <span class="nav-menu__icon" aria-hidden="true"></span>
                    </summary>
                    <nav class="nav-menu__panel" aria-label="Menu utama">
                        @include('layouts.partials.site-links')
                        @auth
                            <a href="{{ route('dashboard') }}" class="nav-menu__account">Buka aplikasi</a>
                        @else
                            <a href="{{ route('login') }}" class="nav-menu__account">Masuk</a>
                        @endauth
                    </nav>
                </details>
            </div>
        </div>
    </header>

    <main class="site-main @yield('main_class')">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container site-footer__inner">
            <span>&copy; {{ date('Y') }} ARTi Framework</span>
            <div class="site-footer__links">
                <a href="{{ route('privasi') }}">Privasi</a>
                <a href="{{ route('syarat') }}">Syarat</a>
                <a href="{{ route('feed') }}">Umpan artikel</a>
            </div>
        </div>
    </footer>
</body>
</html>
