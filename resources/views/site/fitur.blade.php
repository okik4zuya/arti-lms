@extends('layouts.site')

@section('title', 'Fitur')
@section('description', 'Sistem memori, sistem referensi, scratchbook, dan skill ARTi untuk gap map, penulisan naskah, tinjauan sistematis, dan penyiapan gambar.')

@section('content')
<div class="container">
    <section class="hero">
        <p class="hero__eyebrow">Fitur</p>
        <h1>Apa yang ada di dalam ARTi.</h1>
        <p class="hero__lead">Tiga fondasi dan sekumpulan skill yang bekerja di atas Claude Code.</p>
    </section>

    <section class="section">
        <h2>Fondasi</h2>
        <div class="grid">
            <div class="panel">
                <h3>Sistem Memori</h3>
                <p>Setiap proyek punya folder memori: indeks, daftar tugas, status, dan catatan topik. Claude membacanya di awal sesi dan memperbaruinya di akhir.</p>
            </div>
            <div class="panel">
                <h3>Sistem Referensi</h3>
                <p>Satu basis data rujukan per proyek, satu baris per sumber. Teks lengkap diubah ke Markdown agar bisa dicari dan dikutip dengan benar.</p>
            </div>
            <div class="panel">
                <h3>Scratchbook</h3>
                <p>Dokumen bahan mentah yang tersusun per bagian naskah. Temuan sendiri dan literatur diberi penanda berbeda, jadi asal setiap klaim selalu jelas.</p>
            </div>
        </div>
    </section>

    <section class="section">
        <h2>Skill</h2>
        <p class="section__intro">Skill adalah paket instruksi yang dipanggil Claude sesuai tahap pekerjaan.</p>
        <div class="grid">
            @foreach ([
                ['ARTi-setup', 'Onboarding sekali di awal dan penyiapan folder serta memori untuk tiap proyek baru.'],
                ['ARTi-idea', 'Menyusun gap map, menilai kebaruan, merancang eksperimen yang layak, dan memilih jurnal sebelum menulis.'],
                ['ARTi-writing', 'Alur penulisan naskah dari profil jurnal sampai balasan reviewer dan tindak lanjut setelah diterima.'],
                ['ARTi-ref', 'Menambah, memperbarui, dan memeriksa rujukan, termasuk deteksi DOI ganda dan ekstraksi teks lengkap.'],
                ['ARTi-scratchbook', 'Membuat dan mengisi scratchbook, lengkap dengan sistem penanda dan audit kontradiksi.'],
                ['ARTi-SLR', 'Tinjauan pustaka sistematis sesuai PRISMA, dari protokol, penyaringan, sampai sintesis.'],
                ['ARTi-figure', 'Membuat diagram, tabel, dan grafik untuk naskah dari spesifikasi yang bisa diedit.'],
                ['ARTi-jfinder', 'Mencari dan membandingkan jurnal berdasarkan kategori, kuartil, dan lingkup.'],
                ['ARTi-crosscite', 'Memeriksa apakah dua kelompok makalah pernah saling mengutip.'],
                ['ARTi-annotate', 'Menyelesaikan komentar dan saran suntingan di dalam berkas Markdown.'],
            ] as [$name, $text])
                <div class="panel">
                    <h3>{{ $name }}</h3>
                    <p>{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="panel panel--muted">
            <h3>Ebook</h3>
            <p>Seluruh alur ini dijelaskan dalam ebook berbahasa Indonesia yang dibaca langsung di aplikasi.</p>
            <a href="{{ route('harga') }}" class="btn btn--outline">Lihat harga</a>
        </div>
    </section>
</div>
@endsection
