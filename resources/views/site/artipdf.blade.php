@extends('layouts.site')

@section('title', 'ARTi PDF: gabung, pisah, kompres, dan ubah PDF offline di Windows')
@section('description', 'Aplikasi Windows gratis untuk PDF: gabung, pisah, kompres, atur halaman, PDF ke Markdown, dan Markdown ke PDF. Berjalan offline, tanpa unggah dan tanpa akun.')
@section('main_class', 'site-main--flush')

@section('content')
<div class="landing">

    {{-- 1. Hero --}}
    <section class="landing-hero">
        <div class="container">
            <p class="hero__eyebrow">ARTi PDF</p>
            <h1>Gabung, pisah, kompres, dan ubah PDF tanpa mengunggahnya ke mana pun.</h1>
            <p class="hero__lead">Aplikasi Windows gratis. Semua berjalan di komputer Anda: berkas tidak diunggah dan tidak perlu akun.</p>
            <div class="hero__actions">
                <a href="{{ route('artipdf.unduh') }}" class="btn btn--primary btn--lg" data-download @unless (config('site.artipdf_download_url')) target="_blank" rel="noopener" @endunless>Unduh untuk Windows</a>
                <a href="#fitur" class="btn btn--outline btn--lg">Lihat fiturnya</a>
            </div>
            <p class="trust-line">Gratis &middot; Offline &middot; Tanpa akun &middot; Windows 10 dan 11 &middot; Versi {{ config('site.artipdf_version') }}, {{ config('site.artipdf_size') }}</p>
        </div>
    </section>

    {{-- 2. Kenapa offline --}}
    <section class="landing-section">
        <div class="container">
            <h2>Kenapa tanpa unggah?</h2>
            <p>Banyak situs PDF online meminta Anda mengunggah berkas ke server mereka. Untuk brosur, itu tidak masalah. Untuk naskah yang belum terbit, data penelitian, atau berkas yang tidak boleh keluar dari komputer, lebih tenang bila berkasnya memang tidak ke mana-mana.</p>
            <p>ARTi PDF memproses semuanya di komputer Anda, dan hasilnya disimpan di folder yang sama dengan berkas aslinya.</p>
        </div>
    </section>

    {{-- 3. Fitur --}}
    <section class="landing-section landing-section--alt" id="fitur">
        <div class="container">
            <h2>Apa yang bisa dilakukan.</h2>
            <ul class="checklist">
                <li><strong>Gabung (Merge):</strong> satukan beberapa PDF, urutannya bisa diatur dulu.</li>
                <li><strong>Pisah (Split):</strong> per halaman, atau rentang sendiri seperti <code>1-3, 5, 7-9</code>.</li>
                <li><strong>Kompres (Compress):</strong> kecilkan ukuran berkas. Seberapa kecil tergantung isi PDF-nya.</li>
                <li><strong>Page Editor:</strong> seret untuk mengurutkan, putar, atau hapus halaman, bahkan dari beberapa PDF sekaligus.</li>
                <li><strong>PDF ke Markdown:</strong> ambil teks PDF, dengan OCR cadangan untuk berkas hasil pindai.</li>
                <li><strong>Markdown ke PDF:</strong> ubah berkas Markdown menjadi PDF.</li>
            </ul>
            <figure class="figure">
                <img src="{{ asset('images/landing/artipdf-editor.webp') }}" width="778" height="688" loading="lazy"
                     alt="Aplikasi ARTi PDF, tab Page Editor menampilkan thumbnail halaman dari tiga berkas PDF yang bisa diurutkan, diputar, dan dihapus">
                <figcaption><strong>Page Editor.</strong> Halaman dari beberapa PDF dalam satu tampilan.</figcaption>
            </figure>
            <figure class="figure">
                <img src="{{ asset('images/landing/artipdf-merge.webp') }}" width="778" height="688" loading="lazy"
                     alt="Aplikasi ARTi PDF, tab Merge berisi daftar PDF yang akan digabung">
                <figcaption><strong>Merge.</strong> Seret berkas, atur urutan, gabung.</figcaption>
            </figure>
            <figure class="figure">
                <img src="{{ asset('images/landing/artipdf-convert.webp') }}" width="778" height="688" loading="lazy"
                     alt="Aplikasi ARTi PDF, tab PDF ke Markdown dengan dua berkas yang selesai dikonversi">
                <figcaption><strong>PDF ke Markdown.</strong> Hasil disimpan di samping berkas asli. Contoh PDF di gambar adalah berkas contoh, bukan naskah sungguhan.</figcaption>
            </figure>
        </div>
    </section>

    {{-- 4. Klik kanan --}}
    <section class="landing-section">
        <div class="container">
            <h2>Dari klik kanan di Explorer.</h2>
            <p>Installer menambahkan dua perintah ke menu klik kanan pada berkas PDF:</p>
            <ul class="checklist">
                <li><strong>Convert to Markdown:</strong> pilih satu atau banyak PDF sekaligus. Satu jendela konsol menampilkan kemajuan seluruh kelompok.</li>
                <li><strong>Compress PDF:</strong> satu klik, hasilnya disimpan sebagai <code>namaberkas_compressed.pdf</code> di samping aslinya.</li>
            </ul>
            <p class="muted">Menu ini ikut hilang saat aplikasi dihapus lewat uninstaller.</p>
        </div>
    </section>

    {{-- 5. Untuk riset --}}
    <section class="landing-section landing-section--alt" id="riset">
        <div class="container">
            <h2>Untuk membaca jurnal bersama AI.</h2>
            <p>Claude dan asisten AI lain lebih mudah membaca teks Markdown daripada PDF. Mengubah PDF jurnal menjadi Markdown adalah langkah awal sebelum meminta AI merangkum atau mengutip dari makalah itu. Dalam paket ARTi, teks seperti ini menjadi bahan skill literatur.</p>
            <p class="honest"><strong>Jujur:</strong> ini pengambilan teks, bukan konversi sempurna. Tabel dan persamaan tidak dipertahankan, dan teks watermark di tepi halaman bisa ikut sebagai baris pendek. Selalu periksa hasilnya sebelum dikutip.</p>
        </div>
    </section>

    {{-- 6. Jujur --}}
    <section class="landing-section" id="batas">
        <div class="container">
            <h2>Jujur soal batasnya.</h2>
            <ul class="checklist">
                <li><strong>PDF ke Markdown hanya mengambil teks.</strong> Tabel dan persamaan tidak dipertahankan.</li>
                <li><strong>OCR hanya berbahasa Inggris.</strong> PDF hasil pindai berbahasa Indonesia akan lebih sering salah baca.</li>
                <li><strong>Markdown ke PDF butuh Microsoft Edge atau Google Chrome</strong> terpasang di komputer.</li>
                <li><strong>Hanya Windows.</strong> Belum ada versi untuk sistem lain.</li>
                <li><strong>Installer belum bertanda tangan digital,</strong> jadi Windows menampilkan peringatan saat pertama kali dijalankan (lihat langkah di bawah).</li>
            </ul>
            <p class="muted">Kode sumber ARTi PDF berlisensi MIT di <a href="https://github.com/okik4zuya/artipdf" rel="noopener">GitHub</a>. Installer juga membawa Ghostscript, Poppler, dan Tesseract, masing-masing dengan lisensinya sendiri; daftarnya ada di <code>THIRD-PARTY-NOTICES.md</code> di repositori.</p>
        </div>
    </section>

    {{-- 7. Pasang --}}
    <section class="landing-section landing-section--alt" id="pasang">
        <div class="container">
            <h2>Pasang dalam empat langkah.</h2>
            <ol class="steps-list">
                <li>Unduh <code>ArtiPDFSetup-{{ config('site.artipdf_version') }}.exe</code> ({{ config('site.artipdf_size') }}) lewat tombol di bawah.</li>
                <li>Jalankan berkasnya. Bila Windows menampilkan layar biru &ldquo;Windows protected your PC&rdquo; (SmartScreen), klik <strong>More info</strong>, lalu <strong>Run anyway</strong>. Ini muncul karena installer belum bertanda tangan digital, bukan karena ada yang rusak.</li>
                <li>Ikuti installer sampai selesai. Aplikasi muncul di Start Menu.</li>
                <li>Buka ARTi PDF, seret PDF ke jendelanya, atau klik kanan sebuah PDF di Explorer.</li>
            </ol>
            <div class="hero__actions">
                <a href="{{ route('artipdf.unduh') }}" class="btn btn--primary btn--lg" data-download @unless (config('site.artipdf_download_url')) target="_blank" rel="noopener" @endunless>Unduh untuk Windows</a>
            </div>
            <p class="muted">Atau ambil dari <a href="{{ config('site.artipdf_url') }}" rel="noopener">halaman rilis di GitHub</a>.@if (config('site.artipdf_sha256')) Untuk memastikan berkasnya utuh, SHA-256 installer: <code class="sha">{{ config('site.artipdf_sha256') }}</code>@endif</p>
        </div>
    </section>

    {{-- 8. FAQ --}}
    <section class="landing-section">
        <div class="container container--narrow">
            <h2>Pertanyaan yang sering muncul</h2>
            <div class="faq">
                <details>
                    <summary>Aman dipasang?</summary>
                    <p>Kode sumbernya terbuka di GitHub, jadi bisa diperiksa. Peringatan SmartScreen muncul karena installer belum bertanda tangan digital, yang membutuhkan sertifikat berbayar. Unduh hanya dari halaman rilis resmi di atas.</p>
                </details>
                <details>
                    <summary>Di mana hasilnya disimpan?</summary>
                    <p>Di folder yang sama dengan berkas aslinya. Berkas asli tidak diubah.</p>
                </details>
                <details>
                    <summary>Apakah ada untuk sistem selain Windows?</summary>
                    <p>Belum. Rilis terbaru hanya berisi installer Windows.</p>
                </details>
                <details>
                    <summary>Apakah sama dengan arti-pdf di halaman Fitur?</summary>
                    <p>Bukan. Alat <code>arti-pdf</code> di paket ARTi adalah alat internal untuk membuat PDF A4 dari Markdown. ARTi PDF di halaman ini adalah aplikasi Windows tersendiri, yang bisa dipakai tanpa membeli apa pun.</p>
                </details>
                <details>
                    <summary>Berapa kecil hasil kompresnya?</summary>
                    <p>Tergantung isinya. PDF yang penuh gambar biasanya mengecil lebih banyak daripada PDF yang sebagian besar teks. Tidak ada angka pasti yang bisa dijanjikan.</p>
                </details>
            </div>
        </div>
    </section>

    {{-- 9. Bagian dari ARTi --}}
    <section class="landing-closing">
        <div class="container container--narrow">
            <h2>Bagian dari ARTi.</h2>
            <p>ARTi PDF menyiapkan bahan bacaan. ARTi sendiri adalah framework riset berbahasa Indonesia untuk Claude Code, dari ide sampai submit, dengan memori antar sesi dan referensi yang bisa dilacak.</p>
            <div class="hero__actions hero__actions--center">
                <a href="{{ route('free.start') }}" class="btn btn--outline btn--lg">Baca bab 1 gratis</a>
                <a href="{{ route('fitur') }}" class="btn btn--outline btn--lg">Lihat isi ARTi</a>
            </div>
            <p class="disclaimer">ARTi tidak berafiliasi dengan Anthropic. Claude adalah produk Anthropic.</p>
        </div>
    </section>

</div>

{{-- Thank-you dialog, opened by resources/js/subscribe.js when a download button is clicked. The download is a normal link and does not depend on it. --}}
<dialog id="download-thanks" class="thanks-dialog" aria-labelledby="download-thanks-title">
    <h2 id="download-thanks-title">Terima kasih, unduhan dimulai.</h2>
    <ul class="thanks-dialog__tips">
        <li>Jalankan <code>ArtiPDFSetup-{{ config('site.artipdf_version') }}.exe</code> setelah selesai diunduh.</li>
        <li>Bila Windows menampilkan layar biru (SmartScreen), klik <strong>More info</strong>, lalu <strong>Run anyway</strong>.</li>
    </ul>
    <p class="thanks-dialog__offer">Mau dikabari saat ada versi baru?</p>
    <x-subscribe-form source="artipdf" button="Kabari saya" prefix="dlg" />
    <button type="button" class="btn btn--outline thanks-dialog__close" data-dialog-close>Tutup</button>
</dialog>
@endsection

@push('scripts')
    @vite(['resources/js/subscribe.js'])
@endpush
