@extends('layouts.site')

@section('title', 'Kontak')
@section('description', 'Hubungi ARTi untuk pembelian, bantuan instalasi, pengajuan pengembalian dana, dan permintaan penghapusan akun.')

@section('content')
<div class="container container--narrow">
    <section class="hero">
        <p class="hero__eyebrow">Kontak</p>
        <h1>Hubungi ARTi.</h1>
        <p class="hero__lead">Untuk pembelian, bantuan instalasi, <a href="{{ route('syarat') }}#pengembalian">pengajuan pengembalian dana</a>, dan permintaan penghapusan akun.</p>
    </section>

    @if (filled(config('site.seller.name')))
        <div class="panel">
            <p><strong>Penyelenggara:</strong> {{ config('site.seller.name') }}@if (filled(config('site.seller.city'))), {{ config('site.seller.city') }}@endif. ARTi dikelola dan dijual oleh pengembangnya sendiri.</p>
        </div>
    @endif

    <div class="panel">
        @if ($hasWhatsapp)
            @include('site.partials.contact-form', ['idPrefix' => 'kontak'])
        @elseif (! config('site.contact_email'))
            <p>Kanal kontak publik belum dibuka. Ia akan diumumkan di halaman ini.</p>
        @endif

        @if (config('site.contact_email'))
            <p>{{ $hasWhatsapp ? 'Atau lewat email:' : 'Email:' }} <a href="mailto:{{ config('site.contact_email') }}">{{ config('site.contact_email') }}</a></p>
        @endif
        <p>Pembaca e-course yang sudah punya akun bisa <a href="{{ route('login') }}">masuk</a> ke aplikasi.</p>
    </div>
</div>
@endsection
