@extends('layouts.site')

@section('title', 'Harga')
@section('description', 'Rencana tingkat lisensi ARTi: uji coba, individu, lab, akademik, dan situs. Harga diumumkan menjelang rilis.')

@section('content')
<div class="container">
    <section class="hero">
        <p class="hero__eyebrow">Harga</p>
        <h1>Lisensi tahunan, harga menyusul.</h1>
        <p class="hero__lead">Harga belum ditetapkan. Halaman ini menampilkan tingkat lisensi yang direncanakan.</p>
    </section>

    <div class="notice" style="max-width: 680px;">Semua yang ada di halaman ini masih rencana dan bisa berubah sebelum rilis.</div>

    <section class="section" style="padding-top: 0;">
        <div class="grid">
            @foreach ([
                ['Uji coba', 'Mencoba ARTi dengan kunci sementara yang dikirim ke email.'],
                ['Individu', 'Untuk satu peneliti, dengan jumlah perangkat terbatas.'],
                ['Lab', 'Untuk kelompok riset dengan beberapa kursi.'],
                ['Akademik', 'Untuk keperluan pengajaran dan institusi pendidikan.'],
                ['Situs', 'Untuk instalasi di banyak perangkat tanpa pengikatan ke satu mesin.'],
            ] as [$name, $text])
                <div class="panel">
                    <h3>{{ $name }}</h3>
                    <p>{{ $text }}</p>
                    <span class="badge">Harga segera</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="section">
        <h2>Cara kerja lisensi</h2>
        <ul>
            <li>Berlangganan per tahun.</li>
            <li>Kunci diaktifkan pada perangkat saat pertama dipakai.</li>
            <li>Setelah masa berlaku habis, data tetap milik Anda. Yang berhenti adalah pembaruan.</li>
        </ul>
        <p>Pertanyaan? <a href="{{ route('kontak') }}">Hubungi kami</a>.</p>
    </section>
</div>
@endsection
