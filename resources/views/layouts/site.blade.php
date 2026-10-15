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
    @vite(['resources/css/site.css'])
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
                <a href="{{ route('kontak') }}">Kontak</a>
                <a href="{{ route('privasi') }}">Privasi</a>
                <a href="{{ route('syarat') }}">Syarat</a>
                <a href="{{ route('feed') }}">Umpan artikel</a>
            </div>
        </div>
    </footer>

    @php
        $waChat = \App\Support\Offer::whatsappUrl((string) config('site.offer.chat_message'));
        $waBase = \App\Support\Offer::whatsappNumber();
        $waIcon = '<svg viewBox="0 0 32 32" width="28" height="28" aria-hidden="true" focusable="false"><path fill="currentColor" d="M16.003 3C8.83 3 3 8.83 3 16c0 2.29.6 4.52 1.74 6.49L3 29l6.7-1.71A12.95 12.95 0 0 0 16 29c7.17 0 13-5.83 13-13S23.17 3 16.003 3Zm0 23.7c-1.95 0-3.86-.53-5.53-1.52l-.4-.23-3.97 1.01 1.06-3.87-.26-.4A10.64 10.64 0 0 1 5.3 16c0-5.9 4.8-10.7 10.7-10.7S26.7 10.1 26.7 16s-4.8 10.7-10.697 10.7Zm5.87-8c-.32-.16-1.9-.94-2.2-1.05-.3-.1-.51-.16-.73.16-.21.32-.83 1.05-1.02 1.26-.19.22-.37.24-.7.08-.32-.16-1.36-.5-2.59-1.6-.96-.85-1.6-1.9-1.79-2.22-.19-.32-.02-.5.14-.66.14-.14.32-.37.48-.56.16-.19.21-.32.32-.54.1-.21.05-.4-.03-.56-.08-.16-.73-1.76-1-2.4-.26-.63-.53-.54-.73-.55h-.62c-.21 0-.56.08-.85.4-.29.32-1.12 1.1-1.12 2.68 0 1.58 1.15 3.1 1.31 3.32.16.21 2.26 3.45 5.47 4.84.76.33 1.36.53 1.82.68.77.24 1.46.21 2.01.13.61-.09 1.9-.78 2.17-1.53.27-.75.27-1.4.19-1.53-.08-.13-.29-.21-.61-.37Z"/></svg>';
    @endphp
    @if ($waChat)
        @unless (request()->routeIs('kontak'))
            <a href="{{ $waChat }}" class="wa-float" target="_blank" rel="noopener" aria-label="Chat lewat WhatsApp" aria-controls="wa-panel" aria-expanded="false">{!! $waIcon !!}</a>

            <dialog id="wa-panel" class="wa-panel" aria-labelledby="wa-panel-title"
                    data-wa="https://wa.me/{{ $waBase }}"
                    data-templates='@json(\App\Http\Controllers\ContactController::templates())'>
                <div class="wa-panel__head">
                    <span class="wa-panel__icon" aria-hidden="true">{!! $waIcon !!}</span>
                    <span id="wa-panel-title" class="wa-panel__title">Chat dengan ARTi</span>
                    <button type="button" class="wa-panel__close" data-wa-close aria-label="Tutup form">&ndash;</button>
                </div>
                <div class="wa-panel__body">
                    @include('site.partials.contact-form', ['idPrefix' => 'wa', 'topics' => \App\Http\Controllers\ContactController::TOPICS])
                    <p class="wa-panel__direct"><a href="{{ $waChat }}" target="_blank" rel="noopener">Atau langsung buka WhatsApp</a></p>
                </div>
            </dialog>
            @vite(['resources/js/contact-panel.js'])
        @endunless
    @endif
    @stack('scripts')
</body>
</html>
