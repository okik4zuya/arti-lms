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
