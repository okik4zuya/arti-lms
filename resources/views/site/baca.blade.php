@extends('layouts.site')

@inject('offer', 'App\Support\Offer')

@section('title', $title.' — Bab 1 gratis')
@section('description', "Bab 1 e-course ARTi Framework, bagian {$position} dari {$total}: {$title}. Kenapa AI belum memuaskan untuk menulis artikel ilmiah. Bisa dibaca tanpa akun.")

@section('content')
<div class="container container--narrow">
    <p class="article-meta">Bab 1 gratis &middot; bagian {{ $position }} dari {{ $total }}</p>

    <article class="prose">
        {!! $html !!}
    </article>

    <nav class="chapter-nav" aria-label="Navigasi bab">
        @if ($previous)
            <a href="{{ route('free.show', $previous['slug']) }}" class="btn btn--outline">&larr; {{ $previous['title'] }}</a>
        @else
            <span></span>
        @endif
        @if ($next)
            <a href="{{ route('free.show', $next['slug']) }}" class="btn btn--primary">{{ $next['title'] }} &rarr;</a>
        @endif
    </nav>

    {{-- Email sign-up, every page. Pages 1-3 offer the chapter link for later; page 4 offers news. --}}
    @php
        $source = 'baca-'.$position;
        $card = match (true) {
            ! $next => ['Kabari saya bila ada kelanjutannya', 'Tulisan baru, pembaruan alat gratis, dan batch harga berikutnya, langsung ke email Anda. Tidak lebih dari itu, dan Anda bisa berhenti kapan saja.', 'Kabari saya'],
            $position === 2 => ['Dua solusi, dua kelemahan baru. Lalu?', 'Ceritanya berlanjut di bagian berikutnya. Mau kami kirimi tautan bab 1 supaya bisa dilanjutkan nanti, di laptop atau kapan pun?', 'Kirim tautannya'],
            default => ['Mau melanjutkan nanti?', 'Kami kirim tautan bab 1 ke email Anda, supaya bisa dibuka lagi kapan pun.', 'Kirim tautannya'],
        };
    @endphp
    <aside class="panel subscribe-card" id="daftar" data-subscribe-card aria-labelledby="daftar-judul">
        <h3 id="daftar-judul">{{ $card[0] }}</h3>
        <p>{{ $card[1] }}</p>
        <x-subscribe-form :source="$source" :button="$card[2]" />
        @if ($next)
            <button type="button" class="btn btn--link" data-subscribe-dismiss>Tidak sekarang</button>
        @endif
    </aside>

    @unless ($next)
        <div class="panel offer" style="margin-top: var(--space-6);">
            <h3>Sampai di sini bab 1 gratis.</h3>
            <p>Bab-bab berikutnya, kerangka kerja ARTi, dan alat-alatnya ada di paket e-course dan lisensi.</p>
            <p>
                <a href="{{ $offer->buyUrl() }}" class="btn btn--primary" data-kontak="pembelian">Beli lewat WhatsApp</a>
                <a href="{{ route('home') }}#harga" class="btn btn--outline">Lihat harga</a>
            </p>
        </div>
    @endunless
</div>
@endsection

@push('scripts')
    @vite(['resources/js/subscribe.js'])
@endpush
