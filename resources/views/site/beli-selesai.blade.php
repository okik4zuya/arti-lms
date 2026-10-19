@extends('layouts.site')

@section('title', 'Status pembelian')
@section('description', 'Status pembelian ARTi Anda.')
@push('head')
    <meta name="robots" content="noindex">
    @if ($state === 'pending' && ! $failed)
        {{-- Re-check every few seconds while the payment is open; it stops by itself once the order is paid or closed. --}}
        <meta http-equiv="refresh" content="8">
    @endif
@endpush

@section('content')
<div class="container container--narrow">
    <section class="hero">
        @if ($state === 'paid')
            <p class="hero__eyebrow">Pembayaran diterima</p>
            <h1>Cek email Anda.</h1>
            <p class="hero__lead">Akun belajar, tautan untuk mengatur password, kunci lisensi, dan tautan unduhan dikirim ke <strong>{{ $email }}</strong>. Biasanya tiba dalam beberapa menit.</p>
        @elseif ($state === 'pending' && ! $failed)
            <p class="hero__eyebrow">Menunggu pembayaran</p>
            <h1>Pembayaran belum selesai.</h1>
            <p class="hero__lead">Bila Anda baru membayar, tunggu beberapa saat: halaman ini memeriksa ulang sendiri setiap beberapa detik. Bila belum, lanjutkan pembayaran di halaman {{ $gateway->label() }}.</p>
            @if ($order->checkout_url)
                <p><a class="btn btn--primary" href="{{ $order->checkout_url }}">Lanjutkan pembayaran</a></p>
            @endif
        @else
            <p class="hero__eyebrow">Pembayaran tidak selesai</p>
            <h1>Pesanan ini sudah ditutup.</h1>
            <p class="hero__lead">Tidak ada dana yang diambil bila pembayaran tidak selesai. Anda bisa memulai pembelian baru.</p>
            <p><a class="btn btn--primary" href="{{ route('checkout') }}">Beli lagi</a></p>
        @endif
    </section>

    @if ($state === 'paid')
        <div class="panel">
            <ul>
                <li>Tidak menemukan emailnya? Cek folder spam, dan cari pengirim <strong>{{ parse_url(route('home'), PHP_URL_HOST) }}</strong>.</li>
                <li>Password tidak pernah dikirim. Anda memilihnya sendiri lewat tautan di email. Tautannya tidak ada batas waktu dan hanya bisa dipakai sekali. Setelah password diatur, pakai <a href="{{ route('password.request') }}">Lupa password?</a> bila lupa.</li>
                <li>Butuh bantuan? email kami di <a href="mailto:{{ config('site.contact_email') }}">{{ config('site.contact_email') }}</a> atau buka halaman <a href="{{ route('kontak') }}">kontak</a>.</li>
            </ul>
        </div>
    @endif
</div>
@endsection
