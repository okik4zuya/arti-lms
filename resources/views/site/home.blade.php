@extends('layouts.site')

@section('title', 'Framework Riset untuk Claude Code, dari Ide sampai Submit')
@section('description', 'Framework riset untuk Claude Code: referensi yang bisa dilacak, memori antar sesi, gambar dari data, dan naskah Word dengan sitasi Zotero. Termasuk e-course.')

@section('main_class', 'site-main--flush')

@push('head')
    @vite(['resources/js/sticky-buy.js'])
@endpush

@section('content')
<div class="landing">

    {{-- 1. Hero --}}
    <section class="landing-hero">
        <div class="container">
            <p class="hero__eyebrow">ARTi Framework</p>
            <h1>Riset dengan AI yang tidak lupa dan tidak mengarang.</h1>
            <p class="hero__lead">
                Framework riset untuk Claude Code, dari ide sampai publish.
                Dilengkapi e-course untuk mempelajarinya.
            </p>
            <p class="hero__nots">Tanpa referensi yang tidak bisa dilacak. Tanpa mulai dari nol tiap sesi. Tanpa gambar yang terlihat buatan AI. Tanpa copy-paste ke Word.</p>
            <div class="hero__actions">
                <a href="#checkout" class="btn btn--primary btn--lg">Beli sekarang</a>
                <a href="#coba-gratis" class="btn btn--outline btn--lg">Coba gratis</a>
            </div>
            <p class="trust-line">Bayar sekali &middot; Windows &middot; Butuh Claude Code</p>
        </div>
    </section>

    {{-- 2. Masalah --}}
    <section class="landing-section">
        <div class="container">
            <h2>Anda pasti pernah mengalaminya.</h2>
            <p>AI menulis kutipan dengan percaya diri, kelihatan meyakinkan, tapi pas dicek ternyata <strong>referensi tidak ada di jurnal</strong> yang disebut, jadi tetap harus dicek satu per satu.</p>
            <p>Tiap sesi baru AI <strong>mulai dari nol</strong>, jadi literatur harus diunggah ulang, aturan dijelaskan ulang, tujuan diingatkan ulang, padahal kemarin sudah.</p>
            <p>Minta AI buat gambar, tapi <strong>gambarnya terlalu AI</strong>, jadi ragu memasukkan ke naskah.</p>
            <p>Output AI memang "bagus", tapi harus <strong>copy paste</strong> lagi ke word, sekalinya bisa buat word, formatnya tidak sesuai, belum lagi merapikan Zotero.</p>
            <p><strong>Saya juga merasakannya, dan saya membuat solusinya untuk Anda.</strong></p>
        </div>
    </section>

    {{-- 3. Agitasi: empat soal, urutan sama dengan section masalah --}}
    <section class="landing-section landing-section--alt" id="soal">
        <div class="container container--narrow">
            <h2>Empat soal yang terus berulang.</h2>

            <article class="soal">
                <p class="soal__tag">Soal kredibilitas</p>
                <h3>Satu kalimat keliru ikut menyebar.</h3>
                <p>Satu kalimat keliru dari AI masuk ke artikel Anda tanpa dicek ke sumber aslinya.</p>
                <p>Artikel terbit. Orang lain merujuknya. Lalu artikel yang merujuk itu dirujuk lagi.</p>
                <p>Kesalahan itu lama-lama dianggap benar. Bukan karena benar, tapi karena terus disebarkan.</p>
                <ol class="chain" aria-label="Contoh alur penyebaran kesalahan">
                    <li class="chain__step chain__step--bad"><strong>AI menulis kalimat keliru</strong><span>Terdengar yakin, tapi tidak ada di sumbernya.</span></li>
                    <li class="chain__step chain__step--warn"><strong>Masuk ke naskah</strong><span>Tanpa dicek ke sumber aslinya.</span></li>
                    <li class="chain__step"><strong>Artikel terbit</strong><span>Orang lain mulai merujuknya.</span></li>
                    <li class="chain__step"><strong>Dirujuk lagi</strong><span>Artikel yang merujuk ikut dirujuk.</span></li>
                    <li class="chain__step chain__step--end"><strong>Dianggap benar</strong><span>Karena sering dikutip, bukan karena benar.</span></li>
                </ol>
                <p class="figure-note">Ilustrasi alur penyebaran kesalahan, bukan data.</p>
                <p>Yang dipertaruhkan adalah nama Anda di baris penulis.</p>
            </article>

            <article class="soal">
                <p class="soal__tag">Soal tenaga</p>
                <h3>Kerja kemarin terurai lagi.</h3>
                <p>Pukul sebelas malam, sesi baru, dan Anda mengunggah PDF yang sama untuk ketiga kalinya.</p>
                <p>Ada cerita lama tentang Rithah yang memintal benang seharian, lalu menguraikannya lagi tiap malam. Bekerja dengan AI yang tidak punya memori terasa persis begitu. Kerja hari ini tidak pernah jadi pijakan untuk besok.</p>
                <figure class="figure figure--model">
                    <img src="{{ asset('images/landing/benang-terurai.webp') }}" width="1200" height="560" loading="lazy"
                         alt="Robot memintal benang biru menjadi gulungan besar di hari pertama. Di hari kedua dan ketiga, setelah sesi berakhir, gulungannya hilang, benangnya terurai, dan robot memulai gulungan kecil lagi dari awal">
                    <figcaption>Tiap sesi baru, kerja kemarin terurai lagi. Ilustrasi, bukan data.</figcaption>
                </figure>
            </article>

            <article class="soal">
                <p class="soal__tag">Soal gambar</p>
                <h3>Gambar yang Anda minta tiga kali, dan tetap terlihat buatan AI.</h3>
                <p>Gambar buatan AI sering terasa janggal: huruf bergeser, panah tidak nyambung. Anda ragu memasukkannya ke naskah, lalu menggambar ulang di jam yang seharusnya untuk menulis.</p>
            </article>

            <article class="soal">
                <p class="soal__tag">Soal output</p>
                <h3>Naskahnya bagus, tapi belum selesai.</h3>
                <p>Hasil AI disalin ke Word, formatnya bergeser, dan sitasi jadi teks biasa. Ganti gaya jurnal berarti mengetik ulang semuanya, dan itu terulang di tiap submit dan revisi.</p>
            </article>

            <p class="aside">Bukan berarti AI-nya buruk. Alatnya hebat, tapi belum jadi sistem.</p>
        </div>
    </section>

    {{-- 4. Solusi: empat jawaban, pasangan dari empat soal --}}
    <section class="landing-section">
        <div class="container">
            <h2>Solusinya: Claude diajari cara kerja riset, dan diberi alatnya.</h2>
            <p class="section__intro">Claude sudah pintar. Yang belum ia punya adalah cara kerja riset dan perkakas untuk mengerjakannya.</p>
            <figure class="figure figure--model">
                <img src="{{ asset('images/landing/model-skill-alat.webp') }}" width="1200" height="800" loading="lazy"
                     alt="Ilustrasi model AI dengan awan pengetahuan di atas kepalanya, memegang lembar skill di satu tangan dan cangkul di tangan lain, dengan tunas tumbuh di tanah">
            </figure>
            <ul class="legend">
                <li class="legend__item"><strong>Pengetahuan dasar</strong><span>Claude sudah tahu banyak hal, tapi belum tentu tahu cara meneliti dengan benar.</span></li>
                <li class="legend__item"><strong>Skill</strong><span>Lembar cara kerja: langkah riset yang sudah tersusun, dari gap sampai balasan reviewer.</span></li>
                <li class="legend__item"><strong>Alat</strong><span>Perkakas untuk mengerjakannya: cari literatur, ubah PDF, siapkan naskah dan gambar.</span></li>
            </ul>
            <p>Itu yang ARTi beri. Untuk empat soal tadi, jawabannya begini.</p>

            <h3 class="skills-title">Empat soal, empat jawaban</h3>

            <div class="panel answer">
                <p class="soal__tag">Soal kredibilitas</p>
                <h4>Kutipan yang menunjuk ke sumber nyata</h4>
                <p><span class="answer__k">Dulu</span><span>Kutipan dicek satu per satu, karena AI bisa menulis rujukan yang tidak ada.</span></p>
                <p><span class="answer__k answer__k--with">Dengan ARTi</span><span>Satu basis data rujukan per proyek: setiap kutipan menunjuk ke sumber yang benar-benar ada, dan teks lengkap PDF diubah ke Markdown agar bisa dicari. Di Scratchbook, setiap klaim punya penanda asal, dan klaim tanpa sumber tidak boleh masuk naskah.</span></p>
                <p class="answer__gain"><strong>Yang kembali ke Anda:</strong> kualitas, dan tenang saat naskah dicek.</p>
            </div>

            <div class="panel answer">
                <p class="soal__tag">Soal tenaga</p>
                <h4>Sesi berikutnya melanjutkan, bukan mengulang</h4>
                <p><span class="answer__k">Dulu</span><span>Tiap sesi baru: unggah ulang literatur, jelaskan ulang aturan, ingatkan ulang tujuan.</span></p>
                <p><span class="answer__k answer__k--with">Dengan ARTi</span><span>Keputusan dan konteks proyek tersimpan sebagai berkas kecil per topik, dengan indeks. Sesi berikutnya membaca memori itu dan melanjutkan.</span></p>
                <p class="answer__gain"><strong>Yang kembali ke Anda:</strong> waktu dan tenaga.</p>
            </div>

            <div class="panel answer">
                <p class="soal__tag">Soal gambar</p>
                <h4>Gambar dari data, bukan dari imajinasi AI</h4>
                <p><span class="answer__k">Dulu</span><span>Gambar AI yang diragukan asalnya, lalu digambar ulang sendiri.</span></p>
                <p><span class="answer__k answer__k--with">Dengan ARTi</span><span>Diagram, grafik, dan tabel dibuat dengan kode dari data dan spesifikasi yang Anda setujui, bukan digambar mesin. Sumbernya jelas dan hasilnya bisa Anda edit lagi.</span></p>
                <p class="answer__gain"><strong>Yang kembali ke Anda:</strong> kualitas, dan gambar yang tidak Anda ragukan asalnya.</p>
                <p class="muted">Untuk diagram, grafik, tabel, dan gambar gabungan. Bukan untuk foto atau ilustrasi bebas.</p>
            </div>

            <div class="panel answer">
                <p class="soal__tag">Soal output</p>
                <h4>Naskah langsung jadi Word dengan Zotero</h4>
                <p><span class="answer__k">Dulu</span><span>Copy-paste ke Word, format bergeser, sitasi jadi teks biasa.</span></p>
                <p><span class="answer__k answer__k--with">Dengan ARTi</span><span>Naskah Anda ditulis dan diperbaiki bersama AI, lalu hasilnya langsung jadi berkas Word. Sitasinya sudah berupa field Zotero, tanpa <strong>copy paste</strong>.</span></p>
                <p class="callout">ARTi mengeluarkan berkas Word yang sudah berisi field Zotero.</p>
                <p class="answer__gain"><strong>Yang kembali ke Anda:</strong> waktu dan tenaga, di tiap submit dan revisi.</p>
                <p class="muted">Satu klik Refresh di Zotero, dan format sitasi mengikuti gaya yang Anda pilih. Butuh Zotero di Word.</p>
            </div>
        </div>
    </section>

    {{-- 4b. Contoh wujudnya --}}
    <section class="landing-section" id="wujud">
        <div class="container">
            <h2>Seperti apa wujudnya?</h2>
            <p class="section__intro">Bukan kotak hitam. Semuanya berkas biasa di komputer Anda.</p>

            <figure class="pair">
                <img src="{{ asset('images/landing/memori-duo.webp') }}" width="1200" height="520" loading="lazy"
                     alt="Perbandingan dua model AI. Kiri, tanpa memori: model bingung, balasan obrolan kosong, percakapan lama memudar dan ada panah mulai ulang. Kanan, dengan memori: model memegang map, isinya mengalir ke obrolan yang balasannya terisi, dan tunas tumbuh">
                <figcaption class="pair__caps">
                    <span><strong>Tanpa memori</strong>Tiap sesi mulai dari nol. Yang kemarin hilang.</span>
                    <span class="pair__with"><strong>Dengan memori</strong>Sesi berikutnya melanjutkan, bukan mengulang.</span>
                </figcaption>
            </figure>
            <p>Map itu, wujud aslinya seperti ini:</p>

            <figure class="example">
                <figcaption class="example__title"><x-glyph name="folder" class="icon--inline" /> Folder memori satu proyek</figcaption>
                <pre class="example__code" aria-label="Contoh struktur folder memori"><code>memory/
 ├─ MEMORY.md        indeks, satu baris per topik
 ├─ todo-list.md     daftar tugas
 ├─ status.md        kondisi terkini
 └─ memories/
     ├─ journal-target.md
     ├─ introduction-plan.md
     ├─ data-gaps.md
     └─ ...</code></pre>
                <p class="example__note">Struktur asli dari salah satu proyek paper ARTi. Satu berkas, satu topik.</p>
            </figure>

            <figure class="example">
                <figcaption class="example__title"><x-glyph name="catatan" class="icon--inline" /> Scratchbook: setiap klaim punya penanda asal</figcaption>
                <pre class="example__code" aria-label="Contoh baris scratchbook"><code>[LIT: penulis-2024]  Pendopan logam meningkatkan aktivitas fotokatalitik.
[OWN]                Sampel B terurai lebih cepat daripada sampel A.
[SOURCE NEEDED]      Katalis stabil setelah lima siklus.
[DATA NEEDED]        Luas permukaan sampel B.</code></pre>
                <p class="example__note">Contoh format dengan isi rekaan, bukan kutipan dari naskah atau jurnal nyata. Klaim bertanda "perlu sumber" tidak boleh masuk naskah.</p>
            </figure>
        </div>
    </section>

    {{-- 5. Cara kerja --}}
    <section class="landing-section landing-section--alt">
        <div class="container">
            <h2>Amati, Replikasi, Tambah Inovasi.</h2>
            <p class="section__intro">Nama ARTi punya tiga arti. Salah satunya, alur kerja ini.</p>
            <img src="{{ asset('images/landing/diagram-tangga.png') }}" width="1200" height="640" loading="lazy"
                 alt="Tangga tiga anak tangga: Amati, Replikasi, Tambah Inovasi, menuju submit" class="diagram show-desktop">
            <div class="grid grid--3">
                <div class="panel step">
                    <p class="step__label"><x-glyph name="amati" /> Amati</p>
                    <p>Petakan gap dari literatur nyata, ukur kebaruan ide, dan kenali jurnal tujuan sebelum menulis satu kalimat pun.</p>
                </div>
                <div class="panel step">
                    <p class="step__label"><x-glyph name="replikasi" /> Replikasi</p>
                    <p>Uji apakah idenya layak dikerjakan dengan alat yang Anda punya, lalu kumpulkan bahan mentah bertanda sumber.</p>
                </div>
                <div class="panel step">
                    <p class="step__label"><x-glyph name="inovasi" /> Tambah Inovasi</p>
                    <p>Tulis, buat gambar dan tabel, revisi, dan jawab reviewer dengan kebaruan yang diukur, bukan ditebak.</p>
                </div>
            </div>
            <p class="honest"><strong>Jujur:</strong> ARTi membantu prosesnya. Diterima atau tidaknya naskah tetap keputusan jurnal.</p>
            <p class="tagline">Dari ide sampai publish, bersama ARTi.</p>
        </div>
    </section>

    {{-- 6. Demo --}}
    <section class="landing-section">
        <div class="container container--narrow">
            <h2>Lihat caranya bekerja.</h2>
            <div class="video video--file">
                <video controls preload="none" playsinline width="1920" height="1080"
                       poster="{{ asset('images/landing/explainer-poster.webp') }}">
                    <source src="{{ asset('videos/landing/arti-explainer.mp4') }}" type="video/mp4">
                </video>
            </div>
            <p class="video-note">Ringkasan 40 detik, ilustrasi konsep. Demo layar sebenarnya ada di bawah.</p>
            @if (config('site.demo_video_embed_url'))
                <div class="video">
                    <iframe src="{{ config('site.demo_video_embed_url') }}" title="Demo ARTi" loading="lazy" allowfullscreen></iframe>
                </div>
            @else
                <div class="panel panel--muted video-soon">
                    <p><span class="badge">Segera</span></p>
                    <p>Video demo 1-2 menit, rekaman layar apa adanya, sedang disiapkan.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- 7. Isi paket --}}
    <section class="landing-section landing-section--alt">
        <div class="container">
            <h2>Satu paket: e-course dan lisensi.</h2>
            <div class="grid grid--2">
                <div class="panel">
                    <img src="{{ asset('images/landing/ecourse.webp') }}" width="1280" height="800" loading="lazy" class="panel__img"
                         alt="Tampilan e-course di aplikasi: bab Sistem Memori dengan daftar bab dan subbab di sisi kiri">
                    <h3><x-glyph name="kursus" class="icon--inline" /> E-course ARTi Framework</h3>
                    <p>Dibaca langsung di aplikasi, ditulis dari pengalaman memakai ARTi untuk naskah sendiri.</p>
                    <p>Bagian Pendahuluan sudah bisa dibaca. Bagian Amati, Replikasi, dan Tambah Inovasi sedang ditulis, dan bab baru yang terbit ikut Anda dapat tanpa biaya tambahan.</p>
                </div>
                <div class="panel">
                    <img src="{{ asset('images/landing/dashboard.webp') }}" width="1366" height="738" loading="lazy" class="panel__img"
                         alt="Dashboard ARTi di komputer: daftar berkas proyek di kiri, daftar rujukan dengan kuartil jurnal di tengah, dan naskah di kanan (isi naskah diburamkan)">
                    <h3><x-glyph name="lisensi" class="icon--inline" /> Lisensi ARTi Framework</h3>
                    <p>Bayar sekali, berlaku selamanya. 10 skill, dashboard lokal, dan alat pendukung. Berjalan di komputer Anda.</p>
                    <p class="muted">Tampilan dashboard dari proyek sungguhan, isi naskah sengaja diburamkan.</p>
                    <p class="muted">Di balik layar: PDF jadi Markdown yang bisa dicari, ekspor ke Word dan PDF, pencarian literatur lewat OpenAlex, Semantic Scholar, dan Google Scholar.</p>
                </div>
            </div>

            <h3 class="skills-title">Skill, per tahap</h3>
            <div class="grid grid--2">
                <div class="panel">
                    <p class="step__label">Amati</p>
                    <ul class="skill-list">
                        <li><strong>ARTi-idea</strong> memetakan gap dan menilai kebaruan.</li>
                        <li><strong>ARTi-jfinder</strong> mencari dan membandingkan jurnal.</li>
                        <li><strong>ARTi-crosscite</strong> memeriksa apakah dua kelompok makalah pernah saling mengutip.</li>
                    </ul>
                </div>
                <div class="panel">
                    <p class="step__label">Replikasi</p>
                    <ul class="skill-list">
                        <li><strong>ARTi-scratchbook</strong> menampung bahan bertanda sumber.</li>
                        <li><strong>ARTi-ref</strong> mengelola rujukan dan teks lengkap.</li>
                    </ul>
                </div>
                <div class="panel">
                    <p class="step__label">Tambah Inovasi</p>
                    <ul class="skill-list">
                        <li><strong>ARTi-writing</strong> menuntun dari profil jurnal, kerangka, draf, sampai balasan reviewer.</li>
                        <li><strong>ARTi-figure</strong> membuat diagram, tabel, dan grafik.</li>
                        <li><strong>ARTi-annotate</strong> menyelesaikan komentar yang Anda tulis di naskah.</li>
                    </ul>
                </div>
                <div class="panel">
                    <p class="step__label">Persiapan dan review</p>
                    <ul class="skill-list">
                        <li><strong>ARTi-setup</strong> menyiapkan profil Anda dan folder tiap proyek.</li>
                        <li><strong>ARTi-SLR</strong> menemani tinjauan pustaka sistematis berpola PRISMA, dari pertanyaan riset, penyaringan dengan alasan yang tercatat, sampai sintesis.</li>
                    </ul>
                </div>
            </div>
            <p style="margin-top: var(--space-4);"><a href="{{ route('fitur') }}">Lihat semua skill dan alat &rarr;</a></p>
        </div>
    </section>

    {{-- 8. Perbandingan --}}
    <section class="landing-section">
        <div class="container">
            <h2>Apa bedanya dengan Claude Code polos atau alat lain?</h2>
            <p class="section__intro">ARTi tidak bersaing dengan Claude Code. ARTi berjalan di atasnya. Jadi perbandingan yang jujur: Claude Code polos, atau Claude Code yang sudah dipasangi ARTi.</p>
            <div class="table-wrap">
                <table class="compare">
                    <thead><tr><th><span class="sr-only">Aspek</span></th><th>Claude Code polos</th><th>Dengan ARTi</th></tr></thead>
                    <tbody>
                        <tr><th scope="row">Memori antar sesi</th><td>Bisa, tapi strukturnya terserah Anda</td><td>File per topik + indeks, aturan baca dan tulis sudah baku</td></tr>
                        <tr><th scope="row">Anti-ngawur</th><td>Tergantung disiplin Anda</td><td>Wajib bertanda sumber, scratchbook sebagai gerbang</td></tr>
                        <tr><th scope="row">Alur riset</th><td>Prompt dibuat dari nol tiap tahap</td><td>Dipaketkan jadi skill</td></tr>
                        <tr><th scope="row">Gambar</th><td>Tergantung Anda</td><td>Dibuat dari data dan spesifikasi, bisa diedit</td></tr>
                        <tr><th scope="row">Naskah Word dengan sitasi Zotero</th><td>Tergantung Anda</td><td>Berkas Word langsung berisi field Zotero</td></tr>
                    </tbody>
                </table>
            </div>
            <p class="section__intro">Dibanding alat lain:</p>
            <div class="table-wrap">
                <table class="compare">
                    <thead><tr><th><span class="sr-only">Aspek</span></th><th>ARTi</th><th>Claude AI (bawaan)</th><th>NotebookLM</th><th>ChatGPT, Gemini, dll.</th></tr></thead>
                    <tbody>
                        <tr><th scope="row">Memori</th><td>Per topik, tidak bercampur</td><td>Ada, tapi topik bisa bercampur</td><td>Bukan fiturnya</td><td>Bervariasi</td></tr>
                        <tr><th scope="row">Menulis dan mengedit berkas naskah</th><td>Langsung</td><td>Tidak, harus disalin</td><td>Tidak</td><td>Umumnya tidak</td></tr>
                        <tr><th scope="row">Metodologi kerja</th><td>Ada</td><td>Tidak</td><td>Tidak</td><td>Tidak</td></tr>
                    </tbody>
                </table>
            </div>
            <p class="honest"><strong>Jujur:</strong> ARTi butuh setup awal dan kedisiplinan mengisi memori serta scratchbook. Kalau Anda hanya sesekali bertanya ke AI, chat biasa lebih praktis. ARTi terasa manfaatnya saat kerjanya panjang, berbulan-bulan, seperti menulis artikel ilmiah.</p>
        </div>
    </section>

    {{-- 9. Coba gratis --}}
    <section class="landing-section landing-section--alt" id="coba-gratis">
        <div class="container">
            <h2>Kenalan dulu, tanpa membayar.</h2>
            <div class="grid grid--3">
                <div class="panel">
                    <h3>Baca bab 1 e-course</h3>
                    <p>Cerita kenapa AI belum memuaskan untuk menulis artikel ilmiah, dan apa yang saya lakukan.</p>
                    @if ($freeChapterUrl)
                        <a href="{{ $freeChapterUrl }}" class="btn btn--outline">Baca bab 1</a>
                    @else
                        <span class="badge">Segera</span>
                    @endif
                </div>
                <div class="panel">
                    <img src="{{ asset('images/landing/youtube-channel.webp') }}" width="1200" height="675" loading="lazy" class="panel__img"
                         alt="Channel YouTube Dosen BerARTi: Dari ide sampai publish, bersama ARTi">
                    <h3>Tonton di YouTube</h3>
                    <p>Tutorial dan demo langsung di layar di channel Dosen BerARTi, dari mencari gap sampai menyiapkan naskah.</p>
                    <a href="{{ config('site.youtube_url') }}" class="btn btn--outline" rel="noopener">Buka channel Dosen BerARTi</a>
                </div>
                <div class="panel">
                    <img src="{{ asset('images/landing/scipeek-scopus.webp') }}" width="1200" height="750" loading="lazy" class="panel__img"
                         alt="ARTi SciPeek menampilkan kuartil, SJR, dan H-index di samping nama jurnal pada hasil pencarian Scopus">
                    <h3>Pasang ARTi SciPeek</h3>
                    <p>Ekstensi Chrome gratis. Menampilkan kuartil, SJR, dan H-index jurnal langsung di Scopus, ScienceDirect, dan Google Scholar. Berjalan offline, tanpa pelacakan.</p>
                    <a href="{{ route('scipeek') }}" class="btn btn--outline">Lihat ARTi SciPeek</a>
                </div>
                <div class="panel">
                    <img src="{{ asset('images/landing/artipdf-editor.webp') }}" width="778" height="688" loading="lazy" class="panel__img"
                         alt="Aplikasi ARTi PDF di Windows, tab Page Editor menampilkan thumbnail halaman dari tiga berkas PDF yang bisa diurutkan, diputar, dan dihapus">
                    <h3>Pakai ARTi PDF</h3>
                    <p>Aplikasi Windows gratis untuk PDF. Gabung, pisah, kompres, atur halaman, ubah PDF ke Markdown, dan Markdown ke PDF. Berjalan offline: tanpa unggah, tanpa akun.</p>
                    <p class="muted"><strong>Jujur:</strong> ubah PDF ke Markdown di sini hanya mengambil teks, tabel dan persamaan tidak dipertahankan. Installer belum bertanda tangan, jadi Windows bisa menampilkan peringatan.</p>
                    <a href="{{ route('artipdf') }}" class="btn btn--outline">Lihat ARTi PDF</a>
                </div>
                @if (config('site.tools_enabled'))
                <div class="panel">
                    <h3>Buat gambar riset dari hasil AI</h3>
                    <p>Alat gratis di browser: ubah kode dari Gemini, ChatGPT, atau Claude menjadi diagram alur, grafik, dan graphical abstract. Tanpa akun, berkas tidak diunggah.</p>
                    <p class="muted"><strong>Jujur:</strong> hasilnya lebih sederhana daripada ARTi, dan keluaran AI gratis sering perlu diperbaiki beberapa kali.</p>
                    <a href="{{ route('tools.index') }}" class="btn btn--outline">Lihat Tools gratis</a>
                </div>
                @endif
            </div>
        </div>
    </section>

    {{-- 10. Syarat --}}
    <section class="landing-section">
        <div class="container container--narrow">
            <h2>Jujur soal syaratnya.</h2>
            <ul class="checklist">
                <li>Butuh <strong>Claude Code</strong> dan langganan Claude berbayar, dibayar terpisah dari ARTi.</li>
                <li>Butuh <strong>VS Code</strong>.</li>
                <li>Untuk sitasi di berkas Word, butuh <strong>Zotero</strong> di Word.</li>
                <li>Saat ini <strong>Windows saja</strong>.</li>
                <li><strong>Perlu internet saat instalasi</strong> untuk memeriksa lisensi. Kalau lisensi tidak valid atau sudah dipakai di perangkat lain, instalasi berhenti.</li>
                <li><strong>E-course belum lengkap.</strong> Bagian Pendahuluan sudah ada, sisanya sedang ditulis.</li>
                <li>ARTi <strong>tidak menjamin</strong> naskah diterima dan tidak menjanjikan hasil otomatis. Anda tetap yang berpikir dan memeriksa.</li>
            </ul>
        </div>
    </section>

    {{-- 10b. Yang kembali ke Anda --}}
    <section class="landing-section landing-section--alt" id="manfaat">
        <div class="container">
            <h2>Yang kembali ke Anda.</h2>
            <div class="grid grid--2">
                <div class="panel">
                    <h3>Waktu</h3>
                    <p>Tidak mengulang unggahan dan penjelasan, tidak merapikan sitasi dengan tangan.</p>
                </div>
                <div class="panel">
                    <h3>Tenaga</h3>
                    <p>Tidak menggambar ulang, tidak menyalin dan merapikan naskah dari obrolan.</p>
                </div>
                <div class="panel">
                    <h3>Kualitas</h3>
                    <p>Kutipan yang bisa dilacak, gambar dari data, dan sitasi yang mengikuti gaya jurnal.</p>
                </div>
                <div class="panel">
                    <h3>Bayar sekali</h3>
                    <p>Satu kali bayar, berlaku selamanya, tanpa biaya bulanan dari ARTi.</p>
                </div>
            </div>
            <p class="callout"><strong>Pengalaman saya:</strong> tiga paper saya tulis paralel dengan ARTi, sambil menyiapkan topik riset lain.</p>
        </div>
    </section>

    {{-- 10c. Soal langganan Claude --}}
    <section class="landing-section" id="langganan">
        <div class="container container--narrow">
            <h2>Soal langganan Claude.</h2>
            <p>ARTi berjalan di atas Claude Code, yang butuh langganan Claude sendiri: sekitar US$20, kira-kira Rp400.000 per bulan (harga terbaru ada di situs Claude). Terasa berat? Ada tiga cara memandangnya.</p>
            <ul class="checklist">
                <li>Itu sekitar <strong>Rp13.000 per hari</strong>.</li>
                <li>Satu bulan bisa memuat penyusunan naskah, proposal, balasan reviewer, bahan ajar, dan dokumen kerja lain. Ini bukan janji hasil: diterima atau tidaknya naskah tetap keputusan jurnal.</li>
                <li>Anda <strong>tidak harus berlangganan terus</strong>. ARTi sekali bayar, sedangkan langganan Claude bisa dipakai di bulan Anda mengerjakan naskah, lalu dihentikan.</li>
            </ul>
            <p class="honest"><strong>Jujur:</strong> kalau Anda hanya sesekali bertanya ke AI, langganan ini tidak sepadan. Ia terasa manfaatnya saat kerjanya panjang.</p>
        </div>
    </section>

    {{-- 11. Penawaran --}}
    <section class="landing-section landing-section--alt" id="harga">
        <div class="container container--narrow">
            @if ($offer->promotion())
                <h2>{{ $offer->promotion()->label }}</h2>
            @elseif ($offer->launchActive())
                <h2>Harga perintis untuk {{ $offer->quota() }} pembeli pertama.</h2>
            @else
                <h2>Satu paket, satu harga.</h2>
            @endif

            <div class="panel offer">
                <p><strong>Yang Anda dapat:</strong> e-course ARTi Framework &middot; lisensi ARTi Framework &middot; pembaruan ARTi tanpa biaya tambahan.</p>

                @include('site.partials.checkout')
            </div>
        </div>
    </section>

    {{-- 12. FAQ --}}
    <section class="landing-section">
        <div class="container container--narrow">
            <h2>Pertanyaan yang sering muncul</h2>
            <div class="faq">
                <details>
                    <summary>Perlu bisa coding?</summary>
                    <p>Tidak. Anda bekerja dengan Claude Code lewat percakapan. Yang perlu: mau mengikuti struktur folder dan kebiasaan yang diajarkan.</p>
                </details>
                <details>
                    <summary>Apakah ini membuat artikel saya lolos detektor AI?</summary>
                    <p>Tidak, dan ARTi tidak menjanjikannya. Yang ARTi lakukan: setiap klaim punya sumber, ide dan keputusan tetap dari Anda, dan prosesnya tercatat. Itu dasar penggunaan AI yang bisa dipertanggungjawabkan. Cek juga kebijakan AI jurnal tujuan Anda.</p>
                </details>
                <details>
                    <summary>Apakah ARTi menulis artikel untuk saya?</summary>
                    <p>Tidak. ARTi membantu menyusun, mencari, dan memeriksa. Gagasan dan tanggung jawab tetap milik Anda.</p>
                </details>
                <details>
                    <summary>Bagaimana dengan keamanan naskah saya?</summary>
                    <p>Berkas dan basis data ARTi ada di komputer Anda. Tidak ada server ARTi yang menyimpan naskah. Tapi isi yang Anda minta Claude proses tetap dikirim ke Anthropic sesuai layanan Claude Code. Baca kebijakan mereka bila naskah Anda sensitif.</p>
                </details>
                <details>
                    <summary>Bagaimana kalau Claude berubah?</summary>
                    <p>ARTi bergantung pada Claude Code. Kalau aturan atau harganya berubah, ARTi harus menyesuaikan, dan itu risiko yang nyata.</p>
                </details>
                <details>
                    <summary>Apakah ada biaya tahunan?</summary>
                    <p>Tidak. Anda membayar sekali. Skill dan berkas Anda berjalan di komputer Anda sendiri, dan data Anda tetap milik Anda.</p>
                </details>
                <details>
                    <summary>Apakah sitasi bisa diganti ke gaya jurnal lain?</summary>
                    <p>Bisa, selama Anda memakai Zotero di Word. Sitasinya berupa field Zotero, jadi satu klik Refresh dan formatnya mengikuti gaya yang Anda pilih.</p>
                </details>
                <details>
                    <summary>Apakah gambarnya boleh dipakai di jurnal?</summary>
                    <p>Gambar dibuat dari data Anda dan bisa diedit, bukan hasil generator gambar. Tetap cek ketentuan jurnal tujuan Anda. ARTi tidak membuat foto atau ilustrasi bebas.</p>
                </details>
                <details>
                    <summary>Berapa total biayanya?</summary>
                    <p>{{ $offer->format($offer->currentPrice()) }} sekali bayar untuk ARTi, ditambah langganan Claude Anda sendiri. Langganan itu bisa Anda mulai dan hentikan sendiri; lihat <a href="#langganan">Soal langganan Claude</a>.</p>
                </details>
            </div>
        </div>
    </section>

    {{-- 13. Penutup --}}
    <section class="landing-closing">
        <div class="container container--narrow">
            <p class="closing__lines">Referensi yang bisa dilacak. Sesi yang melanjutkan. Gambar dari data. Sitasi yang bisa disegarkan.</p>
            <p class="tagline tagline--lg">Dari ide sampai publish, bersama ARTi.</p>
            <div class="hero__actions hero__actions--center">
                <a href="#checkout" class="btn btn--primary btn--lg">Beli sekarang</a>
                <a href="#coba-gratis" class="btn btn--outline btn--lg">Coba gratis dulu</a>
            </div>
            <p class="disclaimer">ARTi tidak berafiliasi dengan Anthropic. Claude dan Claude Code adalah produk Anthropic.</p>
        </div>
    </section>

</div>

<div class="buy-bar" data-buy-bar hidden>
    <p class="buy-bar__price"><span>Akses selamanya</span> <strong>{{ $offer->format($offer->currentPrice()) }}</strong></p>
    <a href="#checkout" class="btn btn--cta">Dapatkan ARTi</a>
</div>
@endsection
