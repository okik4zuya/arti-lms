@extends('layouts.site')

@inject('offer', 'App\Support\Offer')

@section('title', 'Fitur')
@section('description', 'Memori per topik, referensi asli, scratchbook, 10 skill per tahap riset, dan alat pendukungnya: apa saja isi ARTi dan seperti apa kerjanya di atas Claude Code.')
@section('main_class', 'site-main--flush')

@section('header_cta')
    <a href="{{ route('checkout') }}" class="btn btn--primary header-cta"><span class="cta-long">Beli sekarang</span><span class="cta-short">Beli</span></a>
@endsection

@section('content')
<div class="landing">

    {{-- 1. Hero --}}
    <section class="landing-hero">
        <div class="container">
            <p class="hero__eyebrow">Fitur</p>
            <h1>Apa yang ada di dalam ARTi.</h1>
            <p class="hero__lead">Claude sudah pintar. ARTi memberinya cara kerja riset, perkakas, dan memori. Halaman ini merinci semuanya.</p>
            <nav class="page-nav" aria-label="Isi halaman">
                <a href="#fondasi">Tiga fondasi</a>
                <a href="#skill">10 skill</a>
                <a href="#alat">Alat</a>
                <a href="#dashboard">Dashboard</a>
                <a href="#batas">Batas ARTi</a>
            </nav>
        </div>
    </section>

    {{-- 2. Analogi --}}
    <section class="landing-section">
        <div class="container">
            <h2>Model pintar, diberi cara kerja dan perkakas.</h2>
            <p class="section__intro">Bayangkan seseorang yang sudah berpengetahuan luas. Ia belum tentu tahu cara meneliti dengan benar. Kita ajari keterampilannya, lalu kita beri cangkul.</p>
            <figure class="figure figure--model">
                <video class="figure__video" width="1200" height="800" muted loop playsinline preload="none"
                       poster="{{ asset('images/landing/model-skill-alat.webp') }}"
                       aria-label="Animasi: model AI yang bingung diberi lembar skill dan cangkul, lalu tunas mulai tumbuh">
                    <source src="{{ asset('videos/landing/model-skill-alat.mp4') }}" type="video/mp4">
                </video>
            </figure>
            <ul class="legend">
                <li class="legend__item"><strong>Pengetahuan dasar</strong><span>Sudah ada di Claude. ARTi tidak mengubahnya.</span></li>
                <li class="legend__item"><strong>Skill</strong><span>Lembar cara kerja per tahap riset. Daftarnya di bagian <a href="#skill">10 skill</a>.</span></li>
                <li class="legend__item"><strong>Alat</strong><span>Perkakas untuk mengerjakannya. Daftarnya di bagian <a href="#alat">Alat</a>.</span></li>
            </ul>
            <p>Satu hal lagi yang tidak ada di dalam kepala model: catatan kerja yang tetap ada antar sesi. Itu fondasi pertama di bawah.</p>
        </div>
    </section>

    {{-- 3. Fondasi --}}
    <section class="landing-section landing-section--alt" id="fondasi">
        <div class="container">
            <h2>Tiga fondasi.</h2>
            <p class="section__intro">Tiga kebiasaan kerja yang membuat AI tidak lupa dan tidak mengarang.</p>
            <div class="grid grid--3">
                <div class="panel">
                    <span class="icon-wrap"><x-glyph name="folder" /></span>
                    <h3>Sistem Memori</h3>
                    <dl class="facts">
                        <dt>Masalah</dt><dd>AI lupa antar sesi, jadi kerja kemarin tidak jadi pijakan hari ini.</dd>
                        <dt>Cara kerja</dt><dd>Setiap proyek punya folder <code>memory/</code>: indeks, daftar tugas, status, dan satu berkas per topik. Claude membacanya di awal sesi dan memperbaruinya di akhir.</dd>
                        <dt>Hasil</dt><dd>Sesi berikutnya melanjutkan, bukan mengulang.</dd>
                    </dl>
                </div>
                <div class="panel">
                    <span class="icon-wrap"><x-glyph name="referensi" /></span>
                    <h3>Sistem Referensi</h3>
                    <dl class="facts">
                        <dt>Masalah</dt><dd>Kutipan yang terdengar yakin tapi tidak ada di sumbernya.</dd>
                        <dt>Cara kerja</dt><dd>Satu basis data rujukan per proyek, satu baris per sumber. DOI ganda terdeteksi. Teks lengkap PDF diubah ke Markdown agar bisa dicari dan dikutip dengan benar.</dd>
                        <dt>Hasil</dt><dd>Setiap kutipan menunjuk ke sumber yang benar-benar ada.</dd>
                    </dl>
                </div>
                <div class="panel">
                    <span class="icon-wrap"><x-glyph name="catatan" /></span>
                    <h3>Scratchbook</h3>
                    <dl class="facts">
                        <dt>Masalah</dt><dd>Klaim masuk naskah tanpa jelas asalnya: temuan sendiri, kutipan, atau karangan.</dd>
                        <dt>Cara kerja</dt><dd>Bahan mentah ditimbun per bagian naskah, dengan penanda <code>[OWN]</code>, <code>[LIT: kunci]</code>, <code>[SOURCE NEEDED]</code>, dan <code>[DATA NEEDED]</code>. Ada audit kontradiksi dan kepadatan.</dd>
                        <dt>Hasil</dt><dd>Klaim tanpa sumber tidak boleh masuk naskah.</dd>
                    </dl>
                </div>
            </div>
            <p class="figure-note"><a href="{{ route('home') }}#wujud">Lihat contoh wujud folder memori dan scratchbook</a> di halaman utama.</p>
        </div>
    </section>

    {{-- 4. Skill --}}
    <section class="landing-section" id="skill">
        <div class="container">
            <h2>10 skill, per tahap riset.</h2>
            <p class="section__intro">Skill adalah paket instruksi yang dipanggil Claude sesuai tahap pekerjaan. Buka satu untuk melihat contoh perintah dan hasilnya.</p>
            <p class="figure-note">Contoh perintah disadur dari template prompt ARTi. Isi dalam tanda kurung siku adalah bagian yang Anda ganti.</p>

            @php
                $skills = [
                    'Persiapan dan ide' => [
                        ['ARTi-setup', 'Onboarding sekali di awal dan kerangka memori tiap proyek baru.',
                            'Siapkan ARTi untuk saya. / Buat kerangka memori untuk proyek ini.',
                            'Folder data ARTi dan Profil Peneliti (sekali saja), lalu memori dan <code>CLAUDE.md</code> untuk setiap proyek paper.'],
                        ['ARTi-idea', 'Gap map, penilaian kebaruan, rancangan riset yang diperiksa kelayakannya, dan pemilihan jurnal sebelum menulis.',
                            'Bangun Gap Map dari [folder PDF atau daftar DOI]. / Beri 3 sampai 5 kandidat ide dari Gap 2 dan nilai kebaruannya terhadap profil saya.',
                            'Gap Map, Idea Canvas, skor kebaruan, rancangan riset dengan penanda kelayakan, dan Journal Target Sheet.'],
                        ['ARTi-jfinder', 'Cari dan bandingkan jurnal berdasarkan kategori, kuartil, dan lingkup.',
                            'Cari jurnal Q1 bidang [topik] yang lingkupnya cocok dengan naskah ini.',
                            'Daftar jurnal dengan kuartil, SJR, dan ringkasan lingkup dari basis data Scimago.'],
                        ['ARTi-crosscite', 'Periksa apakah dua kelompok makalah pernah saling mengutip.',
                            'Cek apakah kelompok makalah A dan B pernah saling mengutip sebelum saya klaim keduanya belum terhubung.',
                            'Laporan fakta sitasi antar dua kelompok, dari OpenAlex dengan Semantic Scholar sebagai cadangan.'],
                    ],
                    'Replikasi dan menulis' => [
                        ['ARTi-scratchbook', 'Membuat dan mengisi scratchbook, lengkap dengan penanda dan audit kontradiksi.',
                            'Isi scratchbook bagian [Pendahuluan] dari literatur dan temuan saya.',
                            'Scratchbook bertanda per bagian naskah, audit kontradiksi, dan rencana sesi untuk korpus besar.'],
                        ['ARTi-writing', 'Alur penulisan dari profil jurnal sampai balasan reviewer dan tindak lanjut setelah diterima.',
                            'Tulis [bagian] dari scratchbook, dengan gaya saya. / Tulis cover letter untuk [nama jurnal].',
                            'Journal Profile, Blueprint naskah, draf per bagian, Iteration Log, cover letter, dan balasan reviewer.'],
                        ['ARTi-figure', 'Diagram, tabel, dan grafik naskah dari spesifikasi yang bisa diedit.',
                            'Buat diagram desain studi saya. / Plot jumlah publikasi per tahun dari data ini.',
                            'Diagram <code>.drawio</code> yang bisa diedit, tabel Markdown, grafik PNG, dan figur gabungan (PDF vektor dan PNG berukuran tepat).'],
                    ],
                    'Tinjauan sistematis' => [
                        ['ARTi-SLR', 'Tinjauan pustaka sistematis sesuai PRISMA, dari protokol sampai sintesis.',
                            'Saya mau melakukan systematic review tentang [topik]. Mulai dari protokolnya.',
                            'Protokol, log penyaringan dengan alasan eksklusi, ekstraksi, diagram alir PRISMA, dan matriks sintesis.'],
                    ],
                    'Rujukan dan revisi' => [
                        ['ARTi-ref', 'Tambah, perbarui, dan periksa rujukan, termasuk deteksi DOI ganda dan ekstraksi teks lengkap.',
                            'Tambahkan paper ini ke basis data rujukan: [DOI]. / Ringkas paper [kunci rujukan].',
                            'Satu baris per sumber di basis data proyek, <code>references.md</code>, dan teks lengkap Markdown.'],
                        ['ARTi-annotate', 'Selesaikan komentar dan saran suntingan di dalam berkas Markdown.',
                            'Selesaikan semua komentar saya di naskah ini.',
                            'Komentar dan saran suntingan Anda diterapkan dan ditandai selesai, langsung di berkasnya.'],
                    ],
                ];
            @endphp

            @foreach ($skills as $group => $items)
                <h3 class="skill-group">{{ $group }}</h3>
                <div class="faq skills">
                    @foreach ($items as [$name, $summary, $prompt, $result])
                        <details>
                            <summary><span class="skill__name">{{ $name }}</span><span class="skill__sum">{{ $summary }}</span></summary>
                            <div class="skill__body">
                                <p class="skill__label">Contoh perintah</p>
                                <blockquote class="prompt">{{ $prompt }}</blockquote>
                                <p class="skill__label">Hasilnya</p>
                                <p>{!! $result !!}</p>
                            </div>
                        </details>
                    @endforeach
                </div>
            @endforeach
        </div>
    </section>

    {{-- 5. Alat --}}
    <section class="landing-section landing-section--alt" id="alat">
        <div class="container">
            <h2>Alat di balik layar.</h2>
            <p class="section__intro">Skill memanggil alat-alat ini. Semuanya berjalan di komputer Anda, kecuali pencarian literatur yang butuh internet.</p>

            @php
                $tools = [
                    'Literatur dan rujukan' => [
                        ['arti-lit', 'Basis data rujukan per proyek, dengan ekspor <code>library.md</code> dan <code>references.md</code>.'],
                        ['arti-pdf-ingest', 'Mengubah PDF massal jadi teks Markdown yang bisa dicari, lalu mendaftarkannya ke basis data.'],
                        ['arti-ref-search-mcp', 'Mencari literatur langsung di OpenAlex, Semantic Scholar, dan Google Scholar, menelusuri sitasi, dan mengunduh teks lengkap bila tersedia.'],
                        ['citation-chase', 'Memeriksa apakah kelompok makalah A mengutip atau dikutip kelompok B.'],
                        ['arti-jfinder', 'Basis data Scimago SJR untuk mencari dan membandingkan jurnal.'],
                    ],
                    'Penulisan dan penyuntingan' => [
                        ['arti-docx', 'Naskah Markdown jadi berkas Word <code>.docx</code> yang rapi.'],
                        ['arti-pdf', 'Markdown jadi PDF ukuran A4, dipakai skill untuk menyiapkan dokumen. Berbeda dari aplikasi ARTi PDF di bagian "Kenalan dulu" halaman utama.'],
                        ['arti-annotate', 'Komentar dan saran suntingan langsung di berkas Markdown: baca, terapkan, terima, batalkan, atau bersihkan.'],
                    ],
                    'Gambar dan tabel' => [
                        ['arti-render', 'Spesifikasi JSON jadi diagram <code>.drawio</code> yang bisa diedit.'],
                        ['arti-compose', 'Sumber HTML atau SVG jadi PDF vektor dan PNG berukuran tepat, dengan pemeriksaan tata letak, font, ukuran teks, dan resolusi gambar.'],
                        ['arti-plot', 'Pola grafik matplotlib per proyek, untuk grafik tren dan bibliometrik.'],
                    ],
                    'Proyek dan memori' => [
                        ['arti-db', 'Indeks semua proyek dan bank ide, supaya pertanyaan "apakah saya sudah punya riset tentang ini?" dijawab dari data.'],
                        ['arti-memcheck', 'Pemeriksa susunan memori setiap awal sesi.'],
                    ],
                ];
            @endphp

            @foreach ($tools as $group => $items)
                <details class="tool-group" @if ($loop->first) open @endif>
                    <summary>{{ $group }}</summary>
                    <ul class="tools">
                        @foreach ($items as [$name, $text])
                            <li><span class="tools__name">{{ $name }}</span><span>{!! $text !!}</span></li>
                        @endforeach
                    </ul>
                </details>
            @endforeach

            <details class="tool-group">
                <summary>Konektor luar (didukung, bukan bagian paket)</summary>
                <ul class="tools">
                    <li><span class="tools__name">OpenAlex</span><span>Konektor resmi lewat Claude: pencarian karya, analisis, dan verifikasi daftar pustaka. Perlu login dengan akun Anda sendiri.</span></li>
                </ul>
            </details>
        </div>
    </section>

    {{-- 6. Dashboard --}}
    <section class="landing-section" id="dashboard">
        <div class="container">
            <h2>Dashboard lokal.</h2>
            <p class="section__intro">Berkas proyek, daftar rujukan beserta kuartil jurnalnya, dan naskah, dalam satu layar di komputer Anda.</p>
            <figure class="figure">
                <img src="{{ asset('images/landing/dashboard.webp') }}" width="1366" height="738" loading="lazy"
                     alt="Dashboard ARTi di komputer: daftar berkas proyek di kiri, daftar rujukan dengan kuartil jurnal di tengah, dan naskah di kanan (isi naskah diburamkan)">
                <figcaption>Tampilan dari proyek sungguhan. Isi naskah sengaja diburamkan karena belum terbit.</figcaption>
            </figure>
        </div>
    </section>

    {{-- 7. Batas --}}
    <section class="landing-section landing-section--alt" id="batas">
        <div class="container container--narrow">
            <h2>Yang tidak dilakukan ARTi.</h2>
            <ul class="checklist">
                <li><strong>Tidak menjamin naskah diterima.</strong> ARTi membantu prosesnya. Keputusan ada di jurnal.</li>
                <li><strong>Tidak menulis tanpa sumber.</strong> Klaim tanpa sumber tidak boleh masuk naskah.</li>
                <li><strong>Tidak menjanjikan lolos detektor AI.</strong> Yang dijaga adalah kejujuran asal setiap klaim.</li>
                <li><strong>Tidak menggantikan penilaian Anda.</strong> Anda yang menentukan ide, rancangan, dan kesimpulan.</li>
            </ul>
            <p>Syaratnya: Claude Code dengan langganan Claude berbayar (dibayar terpisah), VS Code, dan Windows. Rinciannya ada di <a href="{{ route('home') }}">halaman utama</a>.</p>
        </div>
    </section>

    {{-- 8. E-course --}}
    <section class="landing-section">
        <div class="container">
            <h2>E-course pendampingnya.</h2>
            <div class="panel panel--muted">
                <span class="icon-wrap"><x-glyph name="kursus" /></span>
                <p>Seluruh alur ini dijelaskan dalam e-course berbahasa Indonesia yang dibaca langsung di aplikasi.</p>
                <p><strong>Statusnya belum lengkap.</strong> Bagian Pendahuluan sudah bisa dibaca. Bagian Amati, Replikasi, dan Tambah Inovasi sedang ditulis, dan bab baru yang terbit ikut Anda dapat tanpa biaya tambahan.</p>
            </div>
        </div>
    </section>

    {{-- 9. Penutup --}}
    <section class="landing-closing">
        <div class="container container--narrow">
            <p class="tagline tagline--lg">Dari ide sampai publish, bersama ARTi.</p>
            <div class="hero__actions hero__actions--center">
                <a href="{{ route('checkout') }}" class="btn btn--primary btn--lg">Beli sekarang</a>
                <a href="{{ route('free.start') }}" class="btn btn--outline btn--lg">Baca bab 1 gratis</a>
            </div>
            <p class="trust-line"><a href="{{ route('home') }}#harga">Lihat harga</a> &middot; Bayar sekali</p>
            <p class="disclaimer">ARTi tidak berafiliasi dengan Anthropic. Claude dan Claude Code adalah produk Anthropic.</p>
        </div>
    </section>

</div>

<script>
    // Looping illustration: it downloads and plays only once it scrolls into view, and never
    // for visitors who prefer reduced motion (the poster stays).
    (function () {
        var v = document.querySelector('.figure__video');
        if (!v) return;
        var calm = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (calm || !('IntersectionObserver' in window)) return;
        new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (e) {
                if (!e.isIntersecting) return;
                v.play().catch(function () {});
                obs.disconnect();
            });
        }, { rootMargin: '200px' }).observe(v);
    })();
</script>
@endsection
