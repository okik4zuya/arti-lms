@extends('layouts.site')

@section('title', 'Kontak')
@section('description', 'Cara menghubungi tim ARTi.')

@section('content')
<div class="container container--narrow">
    <section class="hero">
        <p class="hero__eyebrow">Kontak</p>
        <h1>Hubungi kami.</h1>
    </section>

    <div class="panel">
        @if (config('site.contact_email') || config('site.contact_whatsapp'))
            <ul>
                @if (config('site.contact_email'))
                    <li>Email: <a href="mailto:{{ config('site.contact_email') }}">{{ config('site.contact_email') }}</a></li>
                @endif
                @if (config('site.contact_whatsapp'))
                    <li>WhatsApp: <a href="https://wa.me/{{ preg_replace('/\D+/', '', config('site.contact_whatsapp')) }}">{{ config('site.contact_whatsapp') }}</a></li>
                @endif
            </ul>
        @else
            <p>Kanal kontak publik belum dibuka. Ia akan diumumkan di halaman ini.</p>
        @endif
        <p>Pembaca ebook yang sudah punya akun bisa <a href="{{ route('login') }}">masuk</a> ke aplikasi.</p>
    </div>
</div>
@endsection
