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
            <nav class="site-nav" aria-label="Utama">
                <a href="{{ route('fitur') }}" @if (request()->routeIs('fitur')) aria-current="page" @endif>Fitur</a>
                <a href="{{ route('harga') }}" @if (request()->routeIs('harga')) aria-current="page" @endif>Harga</a>
                <a href="{{ route('unduh') }}" @if (request()->routeIs('unduh')) aria-current="page" @endif>Unduh</a>
                <a href="{{ route('articles.index') }}" @if (request()->routeIs('articles.*')) aria-current="page" @endif>Artikel</a>
                <a href="{{ route('kontak') }}" @if (request()->routeIs('kontak')) aria-current="page" @endif>Kontak</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn--outline">Buka aplikasi</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn--outline">Masuk</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="site-main">
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
