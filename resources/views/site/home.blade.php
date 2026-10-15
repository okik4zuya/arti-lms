@extends('layouts.site')

@section('title', 'Framework Riset untuk Claude Code, dari Ide sampai Submit')
@section('description', 'Framework riset berbahasa Indonesia untuk Claude Code: memori antar sesi, referensi asli yang bisa dilacak, dan alur dari gap sampai submit. Termasuk e-course.')

@section('main_class', 'site-main--flush')

@section('header_cta')
    <a href="{{ $offer->buyUrl() }}" class="btn btn--primary header-cta" data-kontak="pembelian"><span class="cta-long">Beli lewat WhatsApp</span><span class="cta-short">Beli</span></a>
@endsection

@section('content')
<div class="landing">

    {{-- 1. Hero --}}
    <section class="landing-hero">
        <div class="container">
            <p class="hero__eyebrow">ARTi Framework</p>
            <h1>Riset dengan AI yang tidak lupa dan tidak mengarang.</h1>
            <p class="hero__lead">
                Framework riset berbahasa Indonesia untuk Claude Code, dari ide sampai submit.
                Dilengkapi e-course untuk mempelajarinya.
            </p>
            <div class="hero__actions">
                <a href="{{ $offer->buyUrl() }}" class="btn btn--primary btn--lg" data-kontak="pembelian">Beli lewat WhatsApp</a>
                @if ($freeChapterUrl)
                    <a href="{{ $freeChapterUrl }}" class="btn btn--outline btn--lg">Baca bab 1 gratis</a>
                @else
                    <a href="#coba-gratis" class="btn btn--outline btn--lg">Coba gratis dulu</a>
                @endif
            </div>
            <p class="trust-line">Garansi uang kembali {{ $offer->guaranteeDays() }} hari &middot; Windows &middot; Butuh Claude Code</p>
        </div>
    </section>

    {{-- 2. Masalah --}}
    <section class="landing-section">
        <div class="container">
            <h2>Anda pasti pernah mengalaminya.</h2>
            <div class="grid grid--3">
                <div class="panel pain">
                    <span class="icon-wrap"><x-glyph name="ngawur" /></span>
                    <h3>Ngawur, tapi yakin</h3>
                    <p>AI menulis dengan percaya diri. Kutipan terlihat meyakinkan, padahal tidak ada di jurnal yang disebut.</p>
                </div>
                <div class="panel pain">
                    <span class="icon-wrap"><x-glyph name="lupa" /></span>
                    <h3>Sesi baru, mulai dari nol</h3>
                    <p>Unggah ulang literatur, jelaskan ulang aturan, ingatkan ulang tujuan. Padahal kemarin sudah.</p>
                </div>
                <div class="panel pain">
                    <span class="icon-wrap"><x-glyph name="ragu" /></span>
                    <h3>Ragu di setiap tahap</h3>
                    <p>Apakah idenya sudah dikerjakan orang? Cukup baru untuk jurnal ini? Aman dari tuduhan AI? Lolos atau ditolak?</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. Agitasi --}}
    <section class="landing-section landing-section--alt">
        <div class="container container--narrow">
            <h2>Kesalahan kecil yang tidak berhenti.</h2>
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
            <p>Itu baru soal kredibilitas. Soal tenaga, ada cerita lama tentang Rithah yang memintal benang seharian, lalu menguraikannya lagi tiap malam. Bekerja dengan AI yang tidak punya memori terasa persis begitu. Kerja hari ini tidak pernah jadi pijakan untuk besok.</p>
            <figure class="figure figure--model">
                <img src="{{ asset('images/landing/benang-terurai.webp') }}" width="1200" height="560" loading="lazy"
                     alt="Robot memintal benang biru menjadi gulungan besar di hari pertama. Di hari kedua dan ketiga, setelah sesi berakhir, gulungannya hilang, benangnya terurai, dan robot memulai gulungan kecil lagi dari awal">
                <figcaption>Tiap sesi baru, kerja kemarin terurai lagi. Ilustrasi, bukan data.</figcaption>
            </figure>
            <p class="aside">Bukan berarti AI-nya buruk. Alatnya hebat, tapi belum jadi sistem.</p>
        </div>
    </section>

    {{-- 4. Solusi --}}
    <section class="landing-section">
        <div class="container">
            <h2>Bukan alat baru. Cara kerja di atas Claude Code.</h2>
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
            <p>Itu yang ARTi beri. Di atasnya ada tiga fondasi yang menjaga kerjanya tetap benar dan tidak lupa.</p>
            <div class="grid grid--3">
                <div class="panel">
                    <span class="icon-wrap"><x-glyph name="folder" /></span>
                    <h3>Memori per topik</h3>
                    <p>Keputusan dan konteks proyek tersimpan sebagai berkas kecil per topik, dengan indeks. Sesi berikutnya melanjutkan, bukan mengulang.</p>
                </div>
                <div class="panel">
                    <span class="icon-wrap"><x-glyph name="referensi" /></span>
                    <h3>Referensi asli dan bisa dilacak</h3>
                    <p>Satu basis data rujukan per proyek. Setiap kutipan menunjuk ke sumber yang benar-benar ada, dan teks lengkap PDF diubah ke Markdown agar bisa dicari.</p>
                </div>
                <div class="panel">
                    <span class="icon-wrap"><x-glyph name="catatan" /></span>
                    <h3>Scratchbook</h3>
                    <p>Tempat menimbun temuan dan kutipan per bagian naskah, dengan penanda asal setiap klaim. Klaim tanpa sumber tidak boleh masuk naskah.</p>
                </div>
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
                    <p>Dibaca langsung di aplikasi, berbahasa Indonesia, ditulis dari pengalaman memakai ARTi untuk naskah sendiri.</p>
                    <p>Bagian Pendahuluan sudah bisa dibaca. Bagian Amati, Replikasi, dan Tambah Inovasi sedang ditulis, dan bab baru yang terbit selama masa langganan Anda ikut Anda dapat.</p>
                </div>
                <div class="panel">
                    <img src="{{ asset('images/landing/dashboard.webp') }}" width="1366" height="738" loading="lazy" class="panel__img"
                         alt="Dashboard ARTi di komputer: daftar berkas proyek di kiri, daftar rujukan dengan kuartil jurnal di tengah, dan naskah di kanan (isi naskah diburamkan)">
                    <h3><x-glyph name="lisensi" class="icon--inline" /> Lisensi ARTi Framework</h3>
                    <p>Satu tahun. 10 skill, dashboard lokal, dan alat pendukung. Berjalan di komputer Anda.</p>
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
                <li>Saat ini <strong>Windows saja</strong>.</li>
                <li><strong>Perlu internet saat instalasi</strong> untuk memeriksa lisensi. Kalau lisensi tidak valid atau sudah dipakai di perangkat lain, instalasi berhenti.</li>
                <li><strong>E-course belum lengkap.</strong> Bagian Pendahuluan sudah ada, sisanya sedang ditulis.</li>
                <li>ARTi <strong>tidak menjamin</strong> naskah diterima dan tidak menjanjikan hasil otomatis. Anda tetap yang berpikir dan memeriksa.</li>
            </ul>
        </div>
    </section>

    {{-- 11. Penawaran --}}
    <section class="landing-section landing-section--alt" id="harga">
        <div class="container container--narrow">
            @if ($offer->launchActive())
                <h2>Harga perintis untuk {{ $offer->quota() }} pembeli pertama.</h2>
            @else
                <h2>Satu paket, satu harga.</h2>
            @endif

            <div class="panel offer">
                @if ($offer->launchActive())
                    <p class="offer__price">
                        <s>{{ $offer->format($offer->normalPrice()) }}</s>
                        <strong>{{ $offer->format($offer->launchPrice()) }}</strong>
                        <span>per tahun</span>
                    </p>
                    <p class="offer__quota">
                        @if ($offer->remaining() !== null)
                            Tersisa <strong>{{ $offer->remaining() }} dari {{ $offer->quota() }}</strong>.
                        @else
                            Untuk {{ $offer->quota() }} pembeli pertama.
                        @endif
                    </p>
                    <p>Setelah itu harganya {{ $offer->format($offer->normalPrice()) }} per tahun, sekitar {{ $offer->format($offer->perMonthNormal()) }} per bulan. Perpanjangan juga mengikuti harga normal, kecuali sedang ada promo.</p>
                    <p><strong>Kenapa murah sekarang?</strong> E-course masih ditulis. Anda membeli lebih awal dengan harga lebih rendah, dan bab baru ikut Anda dapat selama masa langganan.</p>
                @else
                    <p class="offer__price">
                        <strong>{{ $offer->format($offer->normalPrice()) }}</strong>
                        <span>per tahun</span>
                    </p>
                    <p>Sekitar {{ $offer->format($offer->perMonthNormal()) }} per bulan. Perpanjangan mengikuti harga normal, kecuali sedang ada promo.</p>
                @endif

                <p><strong>Yang Anda dapat:</strong> e-course ARTi Framework &middot; lisensi ARTi Framework satu tahun &middot; pembaruan selama masa langganan.</p>

                <p><strong>Garansi {{ $offer->guaranteeDays() }} hari.</strong> Tidak puas, uang kembali penuh dalam {{ $offer->guaranteeDays() }} hari sejak pembelian, tanpa perlu alasan. Kalau instalasi gagal karena sisi ARTi, uang kembali kapan pun selama masa langganan.</p>

                <p><strong>Cara membeli:</strong> tekan tombol, kirim pesan WhatsApp, lakukan pembayaran, lalu kunci lisensi dan akses e-course dikirim oleh admin.</p>

                <a href="{{ $offer->buyUrl() }}" class="btn btn--primary btn--lg" data-kontak="pembelian">Beli lewat WhatsApp</a>
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
                    <p>ARTi bergantung pada Claude Code. Kalau aturan atau harganya berubah, ARTi harus menyesuaikan, dan itu risiko yang nyata. Karena itu garansi {{ $offer->guaranteeDays() }} hari ada.</p>
                </details>
                <details>
                    <summary>Apa yang terjadi setelah satu tahun?</summary>
                    <p>Anda mendapat peringatan dan masa tenggang sebelum lisensi berakhir. Setelah berakhir, skill dan berkas Anda pada dasarnya tetap bisa dipakai lewat VS Code, tetapi dashboard ARTi tidak bisa dibuka sampai lisensi diperpanjang. Data Anda tetap milik Anda. Perpanjangan mengikuti harga normal, kecuali ada promo.</p>
                </details>
                <details>
                    <summary>Berapa total biayanya?</summary>
                    <p>{{ $offer->format($offer->currentPrice()) }} per tahun untuk ARTi, ditambah langganan Claude Anda sendiri.</p>
                </details>
                <details>
                    <summary>Bagaimana cara minta uang kembali?</summary>
                    <p>Kirim pesan ke WhatsApp admin dalam {{ $offer->guaranteeDays() }} hari sejak pembelian. Kunci dicabut dan uang dikembalikan.</p>
                </details>
            </div>
        </div>
    </section>

    {{-- 13. Penutup --}}
    <section class="landing-closing">
        <div class="container container--narrow">
            <p class="tagline tagline--lg">Dari ide sampai publish, bersama ARTi.</p>
            <div class="hero__actions hero__actions--center">
                <a href="{{ $offer->buyUrl() }}" class="btn btn--primary btn--lg" data-kontak="pembelian">Beli lewat WhatsApp</a>
                <a href="#coba-gratis" class="btn btn--outline btn--lg">Coba gratis dulu</a>
            </div>
            <p class="disclaimer">ARTi tidak berafiliasi dengan Anthropic. Claude dan Claude Code adalah produk Anthropic.</p>
        </div>
    </section>

</div>
@endsection
