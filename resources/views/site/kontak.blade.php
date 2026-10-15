@extends('layouts.site')

@section('title', 'Kontak')
@section('description', 'Hubungi ARTi lewat WhatsApp untuk pembelian, bantuan instalasi, dan permintaan uang kembali dalam garansi 7 hari.')

@section('content')
<div class="container container--narrow">
    <section class="hero">
        <p class="hero__eyebrow">Kontak</p>
        <h1>Hubungi ARTi.</h1>
        <p class="hero__lead">Untuk pembelian, bantuan instalasi, permintaan uang kembali (garansi {{ config('site.offer.guarantee_days') }} hari sejak pembelian), dan permintaan penghapusan akun.</p>
    </section>

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
