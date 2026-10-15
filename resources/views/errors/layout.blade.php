{{-- Standalone on purpose: no session, @auth or database use, so an error page never fails because of the thing that failed. --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('heading') — ARTi Framework</title>
    <meta name="robots" content="noindex">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    @vite(['resources/css/site.css'])
</head>
<body>
    <a href="#isi" class="skip-link">Lewati ke isi</a>

    <header class="site-header">
        <div class="container site-header__inner">
            <a href="{{ route('home') }}" class="site-brand" aria-label="ARTi — beranda">
                <img src="{{ asset('images/wordmark-site.webp') }}" alt="ARTi" class="site-brand__logo" width="73" height="32">
            </a>
        </div>
    </header>

    <main id="isi" tabindex="-1" class="site-main">
        <div class="container container--narrow">
            <section class="hero">
                <p class="hero__eyebrow">@yield('code')</p>
                <h1>@yield('heading')</h1>
                <p class="hero__lead">@yield('message')</p>
                <div class="hero__actions">
                    <a href="{{ route('home') }}" class="btn btn--primary">Ke beranda</a>
                    @hasSection('extra')
                        @yield('extra')
                    @else
                        <a href="{{ route('free.start') }}" class="btn btn--outline">Baca bab 1 gratis</a>
                    @endif
                </div>
            </section>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container site-footer__inner">
            <span>&copy; {{ date('Y') }} ARTi Framework</span>
            <div class="site-footer__links">
                <a href="{{ route('kontak') }}">Kontak</a>
                <a href="{{ route('privasi') }}">Privasi</a>
                <a href="{{ route('syarat') }}">Syarat</a>
            </div>
        </div>
    </footer>
</body>
</html>
