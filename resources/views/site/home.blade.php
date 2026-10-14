@extends('layouts.site')

@section('title', 'Riset yang tidak lupa dan tidak mengarang')
@section('description', 'ARTi Framework membantu peneliti bekerja dengan Claude Code: memori antar sesi, rujukan yang bisa dilacak, dan alur Amati, Replikasi, Tambah Inovasi.')

@section('content')
<div class="container">
    <section class="hero">
        <p class="hero__eyebrow">ARTi Framework</p>
        <h1>Riset dengan AI yang tidak lupa dan tidak mengarang.</h1>
        <p class="hero__lead">
            ARTi adalah sistem kerja untuk Claude Code. Ia memberi AI memori yang tersimpan antar sesi
            dan rujukan yang bisa dilacak, supaya pekerjaan tidak mulai dari nol dan tidak ada kutipan yang karangan.
        </p>
        <div class="hero__actions">
            <a href="{{ route('fitur') }}" class="btn btn--primary">Lihat fitur</a>
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn--outline">Buka ebook</a>
            @else
                <a href="{{ route('login') }}" class="btn btn--outline">Masuk untuk membaca ebook</a>
            @endauth
        </div>
    </section>

    <section class="section">
        <h2>Tiga langkah, satu alur kerja</h2>
        <p class="section__intro">Nama ARTi punya tiga arti. Salah satunya adalah alur kerjanya.</p>
        <div class="grid">
            <div class="panel">
                <h3>Amati</h3>
                <p>Baca literatur, petakan celah, dan kenali jurnal tujuan sebelum menulis satu kalimat pun.</p>
            </div>
            <div class="panel">
                <h3>Replikasi</h3>
                <p>Uji dulu apakah idenya layak dikerjakan, lalu kumpulkan bahan mentah di satu tempat.</p>
            </div>
            <div class="panel">
                <h3>Tambah Inovasi</h3>
                <p>Tulis, revisi, dan jawab reviewer dengan kebaruan yang diukur, bukan ditebak.</p>
            </div>
        </div>
    </section>

    <section class="section">
        <h2>Tiga fondasi</h2>
        <div class="grid">
            <div class="panel">
                <h3>Sistem Memori</h3>
                <p>Keputusan dan konteks proyek tersimpan sebagai berkas, jadi sesi berikutnya melanjutkan, bukan mengulang.</p>
            </div>
            <div class="panel">
                <h3>Sistem Referensi</h3>
                <p>Satu basis data rujukan per proyek. Setiap kutipan menunjuk ke sumber yang benar-benar ada.</p>
            </div>
            <div class="panel">
                <h3>Scratchbook</h3>
                <p>Tempat menimbun temuan dan literatur per bagian naskah, dengan penanda asal setiap klaim.</p>
            </div>
        </div>
        <p style="margin-top: var(--space-5);"><a href="{{ route('fitur') }}">Selengkapnya di halaman fitur &rarr;</a></p>
    </section>

    <section class="section">
        <div class="panel panel--muted">
            <h3>Tulisan terbaru</h3>
            @if ($latest->isEmpty())
                <p>Belum ada artikel. <a href="{{ route('articles.index') }}">Lihat halaman artikel</a>.</p>
            @else
                <ul class="article-list" style="margin-top: var(--space-3);">
                    @foreach ($latest as $article)
                        <li class="article-list__item">
                            <h3 class="article-list__title"><a href="{{ route('articles.show', $article->slug) }}">{{ $article->title }}</a></h3>
                            <p class="article-meta" style="margin: 0;">{{ $article->date->locale('id')->translatedFormat('j F Y') }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>
</div>
@endsection
