@extends('layouts.site')

@inject('offer', 'App\Support\Offer')

@section('title', 'Harga')
@section('description', 'Satu paket e-course dan lisensi ARTi Framework, Rp199.000 per tahun. Harga perintis untuk 100 pembeli pertama.')

@section('content')
<div class="container container--narrow">
    <section class="hero">
        <p class="hero__eyebrow">Harga</p>
        <h1>Satu paket, lisensi tahunan.</h1>
        <p class="hero__lead">E-course ARTi Framework dan lisensi ARTi Framework dijual bersama.</p>
    </section>

    <div class="panel offer">
        @if ($offer->launchActive())
            <p class="offer__price">
                <s>{{ $offer->format($offer->normalPrice()) }}</s>
                <strong>{{ $offer->format($offer->launchPrice()) }}</strong>
                <span>per tahun, harga perintis</span>
            </p>
            <p class="offer__quota">
                @if ($offer->remaining() !== null)
                    Tersisa <strong>{{ $offer->remaining() }} dari {{ $offer->quota() }}</strong>.
                @else
                    Untuk {{ $offer->quota() }} pembeli pertama.
                @endif
            </p>
            <p>Setelah itu harganya {{ $offer->format($offer->normalPrice()) }} per tahun, sekitar {{ $offer->format($offer->perMonthNormal()) }} per bulan. Perpanjangan mengikuti harga normal, kecuali sedang ada promo.</p>
        @else
            <p class="offer__price">
                <strong>{{ $offer->format($offer->normalPrice()) }}</strong>
                <span>per tahun</span>
            </p>
            <p>Sekitar {{ $offer->format($offer->perMonthNormal()) }} per bulan. Perpanjangan mengikuti harga normal, kecuali sedang ada promo.</p>
        @endif

        <p><strong>Isi paket:</strong> e-course ARTi Framework &middot; lisensi ARTi Framework satu tahun (10 skill, dashboard lokal, alat pendukung) &middot; pembaruan selama masa langganan.</p>
        <p><strong>Garansi {{ $offer->guaranteeDays() }} hari.</strong> Uang kembali penuh dalam {{ $offer->guaranteeDays() }} hari sejak pembelian, tanpa perlu alasan.</p>
        <a href="{{ $offer->buyUrl() }}" class="btn btn--primary btn--lg">Beli lewat WhatsApp</a>
    </div>

    <section class="section">
        <h2>Cara kerja lisensi</h2>
        <ul>
            <li>Admin mengirim kunci lisensi lewat WhatsApp setelah pembayaran dikonfirmasi.</li>
            <li>Kunci diperiksa lewat internet saat instalasi. Kunci yang tidak valid atau sudah dipakai di perangkat lain membuat instalasi berhenti.</li>
            <li>Setelah masa berlaku habis, data tetap milik Anda. Dashboard ARTi tidak bisa dibuka sampai lisensi diperpanjang.</li>
        </ul>
        <p>Selain paket ini, Anda memerlukan Claude Code dan langganan Claude sendiri. Pertanyaan? <a href="{{ route('kontak') }}">Hubungi kami</a>.</p>
    </section>
</div>
@endsection
