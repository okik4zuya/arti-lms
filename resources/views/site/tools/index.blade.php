@extends('layouts.site')

@section('title', 'Tools gratis: buat diagram, grafik, dan graphical abstract dari hasil AI')
@section('description', 'Alat gratis di browser untuk mengubah keluaran Gemini, ChatGPT, atau Claude menjadi diagram, grafik, dan gambar gabungan siap naskah. Tanpa akun, berkas tidak diunggah.')
@section('main_class', 'site-main--flush')

@push('head')
    @unless (config('site.tools_enabled'))
        <meta name="robots" content="noindex">
    @endunless
@endpush

@section('content')
<div class="landing">

    {{-- 1. Hero --}}
    <section class="landing-hero">
        <div class="container">
            <p class="hero__eyebrow">Tools gratis</p>
            <h1>Buat gambar riset dari hasil AI, gratis dan tanpa unggah.</h1>
            <p class="hero__lead">AI menulis kodenya, alat di halaman ini mengubahnya menjadi diagram, grafik, atau graphical abstract. Semua berjalan di browser Anda.</p>
            <div class="hero__actions">
                <a href="#alat" class="btn btn--primary btn--lg">Pilih alat</a>
                <a href="#pilih" class="btn btn--outline btn--lg">Bingung memilih?</a>
            </div>
            <p class="trust-line">Gratis &middot; Tanpa akun &middot; Berkas tidak diunggah &middot; Untuk Gemini, ChatGPT, atau Claude</p>
        </div>
    </section>

    {{-- 2. Cara kerja --}}
    <section class="landing-section">
        <div class="container">
            <h2>Cara kerjanya.</h2>
            <ol class="steps-list">
                <li><strong>Ceritakan gambarnya ke AI.</strong> Pakai Gem yang saya siapkan, atau tempel instruksinya ke AI mana pun.</li>
                <li><strong>AI menjawab dengan kode,</strong> bukan gambar: JSON untuk diagram dan grafik, HTML/SVG untuk gambar gabungan.</li>
                <li><strong>Tempel kodenya di alat.</strong> Gambar muncul, lengkap dengan pemeriksaan. Bila ada yang salah, satu tombol menyalin keluhannya untuk ditempel kembali ke AI.</li>
            </ol>
            <p>Hasil akhirnya berkas biasa: PNG, SVG, PDF, atau .drawio yang bisa Anda edit.</p>
        </div>
    </section>

    {{-- 3. Alat --}}
    <section class="landing-section landing-section--alt" id="alat">
        <div class="container">
            <h2>Empat alat.</h2>
            <div class="grid grid--2">
                @foreach ($tools as $slug => $tool)
                    <div class="panel">
                        <p class="soal__tag">{{ $tool['lane'] }}</p>
                        <h3>{{ $tool['title'] }}</h3>
                        <p>{{ $tool['summary'] }}</p>
                        <a href="{{ route('tools.show', $slug) }}" class="btn btn--outline">Buka {{ $tool['name'] }}</a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 4. Memilih --}}
    <section class="landing-section" id="pilih">
        <div class="container">
            <h2>Bingung memilih?</h2>
            <ul class="checklist">
                <li><strong>Kotak dan panah</strong> (desain studi, alur penyaringan, tahapan metode): <a href="{{ route('tools.show', 'diagram') }}">Diagram alur</a>.</li>
                <li><strong>Angka</strong> (jumlah per tahun, perbandingan kelompok, tren): <a href="{{ route('tools.show', 'plot') }}">Grafik data</a>.</li>
                <li><strong>Beberapa panel (a)(b)(c), graphical abstract, atau ukuran piksel tertentu:</strong> <a href="{{ route('tools.show', 'composite') }}">Gambar gabungan</a>.</li>
                <li><strong>Jurnal mana yang cocok dan apa syaratnya:</strong> <a href="{{ route('tools.show', 'profil-jurnal') }}">Profil jurnal</a>.</li>
            </ul>
            <p class="muted">Bila sebuah diagram boleh Anda revisi sendiri, pilih Diagram alur; Gambar gabungan hanya untuk yang tidak muat di sana (ikon, lengkung, tata letak bebas).</p>
        </div>
    </section>

    {{-- 5. Jujur --}}
    <section class="landing-section landing-section--alt" id="batas">
        <div class="container">
            <h2>Jujur soal batasnya.</h2>
            <ul class="checklist">
                <li><strong>Hasilnya lebih sederhana daripada ARTi.</strong> Tidak ada agen yang menjalankan alat dan memeriksa gambarnya, tidak ada data proyek, tidak ada verifikasi sumber.</li>
                <li><strong>AI gratis sering salah di percobaan pertama.</strong> Karena itu setiap alat menampilkan kesalahan dalam bentuk yang bisa ditempel kembali ke AI.</li>
                <li><strong>Angka tetap tanggung jawab Anda.</strong> Alat menggambar apa yang ditulis AI; ia tidak mengecek apakah isinya benar.</li>
                <li><strong>Foto, mikrograf, dan gel tidak ada di jalur ini.</strong> Gambar eksperimen hanya boleh diberi label, skala, atau dipotong, tidak diolah.</li>
            </ul>
        </div>
    </section>

    {{-- 6. Privasi --}}
    <section class="landing-section">
        <div class="container">
            <h2>Privasi.</h2>
            <p>Alat berjalan seluruhnya di browser Anda. Server hanya mengirim halaman; kode yang Anda tempel, data grafik, dan gambar hasilnya tidak dikirim ke mana pun. Cara kerja AI-nya (Gemini, ChatGPT, atau Claude) mengikuti kebijakan masing-masing penyedia.</p>
        </div>
    </section>

    {{-- 7. FAQ --}}
    <section class="landing-section landing-section--alt">
        <div class="container container--narrow">
            <h2>Pertanyaan yang sering muncul</h2>
            <div class="faq">
                <details>
                    <summary>Harus pakai Gem?</summary>
                    <p>Tidak. Gem hanya cara praktis di Gemini. Instruksi yang sama bisa ditempel di awal percakapan di ChatGPT atau Claude.</p>
                </details>
                <details>
                    <summary>Kenapa AI menjawab dengan kode, bukan gambar?</summary>
                    <p>Gambar dari AI sulit diubah dan sering terlihat &ldquo;terlalu AI&rdquo;. Kode bisa diperiksa, diperbaiki, dan diubah menjadi berkas yang bersih.</p>
                </details>
                <details>
                    <summary>Apakah hasilnya boleh dipakai di artikel?</summary>
                    <p>Boleh, setelah Anda memeriksa isi dan angkanya. Ikuti aturan gambar dan lisensi jurnal Anda sendiri.</p>
                </details>
                <details>
                    <summary>Apa bedanya dengan ARTi berbayar?</summary>
                    <p>Di ARTi, Claude Code menjalankan alat, melihat hasilnya, memperbaiki sendiri, dan mengingat proyek Anda. Di sini Anda yang menjadi perantaranya.</p>
                </details>
            </div>
        </div>
    </section>

    @include('site.tools.partials.cta')

</div>
@endsection

@push('scripts')
    @vite(['resources/js/subscribe.js'])
@endpush
