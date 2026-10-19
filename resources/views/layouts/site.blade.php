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
    <meta property="og:image:alt" content="ARTi Framework: riset dengan AI yang tidak lupa dan tidak mengarang">
    <meta property="og:locale" content="id_ID">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="alternate" type="application/atom+xml" title="Artikel ARTi" href="{{ route('feed') }}">


    <link rel="preload" href="{{ asset('fonts/figtree/figtree-latin-400-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/figtree/figtree-latin-700-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    <script>
        // Anchor jumps need real section heights, so turn off content-visibility (see site.css) for them.
        (function () {
            var d = document.documentElement, off = function () { d.classList.add('no-cv'); };
            if (location.hash) off();
            addEventListener('hashchange', off);
            document.addEventListener('click', function (e) {
                var a = e.target.closest && e.target.closest('a[href*="#"]');
                if (a) off();
            }, true);
        })();
    </script>
    @vite(['resources/css/site.css', 'resources/js/submit-loading.js'])
    @stack('head')
</head>
<body>
    <a href="#isi" class="skip-link">Lewati ke isi</a>

    <header class="site-header">
        <div class="container site-header__inner">
            <a href="{{ route('home') }}" class="site-brand" aria-label="ARTi — beranda">
                <img src="{{ asset('images/wordmark-site.webp') }}" alt="ARTi" class="site-brand__logo" width="73" height="32">
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

    <main id="isi" tabindex="-1" class="site-main @yield('main_class')">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container site-footer__inner">
            <span>&copy; {{ date('Y') }} ARTi Framework</span>
            <div class="site-footer__links">
                @if (filled(config('site.contact_email')))
                    <a href="mailto:{{ config('site.contact_email') }}">{{ config('site.contact_email') }}</a>
                @endif
                <a href="{{ route('kontak') }}">Kontak</a>
                <a href="{{ route('privasi') }}">Privasi</a>
                <a href="{{ route('syarat') }}">Syarat</a>
                <a href="{{ route('feed') }}">Umpan artikel</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
