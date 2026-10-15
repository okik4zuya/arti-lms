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
            <form method="POST" action="{{ route('kontak.send') }}" class="stack" novalidate>
                @csrf

                <div class="form-field">
                    <label for="nama" class="form-label">Nama</label>
                    <input id="nama" name="nama" type="text" class="form-input" value="{{ old('nama') }}" maxlength="80" required autocomplete="name" @error('nama') aria-invalid="true" aria-describedby="nama-error" @enderror>
                    @error('nama') <span id="nama-error" class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="topik" class="form-label">Keperluan</label>
                    <select id="topik" name="topik" class="form-input" required @error('topik') aria-invalid="true" aria-describedby="topik-error" @enderror>
                        @foreach ($topics as $value => $label)
                            <option value="{{ $value }}" @selected(old('topik') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('topik') <span id="topik-error" class="form-error">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="pesan" class="form-label">Pesan</label>
                    <textarea id="pesan" name="pesan" rows="5" class="form-input" maxlength="1000" required @error('pesan') aria-invalid="true" aria-describedby="pesan-error" @enderror>{{ old('pesan') }}</textarea>
                    @error('pesan') <span id="pesan-error" class="form-error">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="btn btn--primary">Kirim lewat WhatsApp</button>
                <p class="muted">Tombol ini membuka WhatsApp dengan pesan yang sudah terisi. Pesan baru terkirim setelah Anda menekan kirim di WhatsApp, dan tidak disimpan di situs ini.</p>
            </form>
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
