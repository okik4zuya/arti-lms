@extends('layouts.site')

@section('title', $tool['title'].' gratis: '.$tool['name'].' dengan Gem dan alat browser')
@section('description', $tool['summary'])
@section('main_class', 'site-main--flush')

@push('head')
    @unless (config('site.tools_enabled'))
        <meta name="robots" content="noindex">
    @endunless
@endpush

@section('content')
@php
    $hasWorkbench = filled($tool['entry']);
    $steps = $hasWorkbench
        ? ['Pasang Gem (atau tempel instruksinya ke AI mana pun) dan ceritakan gambar yang Anda mau.', 'Salin kode yang dihasilkan, tempel di kotak alat di bawah.', 'Bila ada kesalahan, tekan Salin kesalahan, tempel ke AI, ulangi. Lalu unduh hasilnya.']
        : ['Pasang Gem (atau tempel instruksinya ke AI mana pun).', 'Tempel aims and scope, panduan penulis, dan contoh artikel dari jurnal target.', 'Baca profil yang dihasilkan dan periksa bagian yang bertanda belum diverifikasi.'];
@endphp
<div class="landing">

    <section class="landing-hero">
        <div class="container">
            <p class="hero__eyebrow"><a href="{{ route('tools.index') }}">Tools gratis</a> &rsaquo; {{ $tool['name'] }}</p>
            <h1>{{ $tool['title'] }}: dari AI ke berkas siap pakai.</h1>
            <p class="hero__lead">{{ $tool['summary'] }}</p>
            <p class="trust-line">Gratis &middot; Tanpa akun &middot; Berkas tidak diunggah ke server</p>
        </div>
    </section>

    <section class="landing-section">
        <div class="container">
            <h2>Tiga langkah.</h2>
            <ol class="steps-list">
                @foreach ($steps as $step)
                    <li>{{ $step }}</li>
                @endforeach
            </ol>
        </div>
    </section>

    @include('site.tools.partials.gem-block')

    @if ($hasWorkbench)
        <section class="landing-section" id="alat">
            <div class="container">
                <h2>Alatnya.</h2>
                @include('site.tools.partials.workbench-'.$slug)
            </div>
        </section>
    @endif

    <section class="landing-section {{ $hasWorkbench ? 'landing-section--alt' : '' }}" id="batas">
        <div class="container">
            <h2>Jujur soal batasnya.</h2>
            <ul class="checklist">
                @foreach ($tool['limits'] as $item)
                    <li>{!! $item !!}</li>
                @endforeach
                <li><strong>Hasilnya lebih sederhana daripada ARTi.</strong> Tidak ada agen yang memeriksa ulang, tidak ada memori proyek, dan tidak ada verifikasi sumber. Keluaran AI gratis sering perlu diperbaiki; itulah gunanya tombol Salin kesalahan.</li>
            </ul>
        </div>
    </section>

    @include('site.tools.partials.cta')

</div>
@endsection

@push('scripts')
    @vite(array_values(array_filter(['resources/js/tools/gem.js', $tool['entry']])))
    @vite(['resources/js/subscribe.js'])
@endpush
