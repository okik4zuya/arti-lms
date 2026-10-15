@extends('layouts.site')

@section('title', 'ARTi SciPeek: cek kuartil jurnal di Scopus dan Google Scholar')
@section('description', 'Ekstensi Chrome gratis yang menampilkan kuartil, SJR, dan H-index jurnal langsung di hasil pencarian Scopus, ScienceDirect, Google Scholar, dan halaman artikel. Offline, tanpa akun.')
@section('main_class', 'site-main--flush')

@section('content')
<div class="landing">

    {{-- 1. Hero --}}
    <section class="landing-hero">
        <div class="container">
            <p class="hero__eyebrow">ARTi SciPeek</p>
            <h1>Cek kuartil jurnal Scopus tanpa buka tab baru.</h1>
            <p class="hero__lead">Ekstensi Chrome gratis. Di samping nama jurnal pada hasil pencarian muncul lencana kuartil (Q1 sampai Q4), SJR, dan H-index dari Scimago.</p>
            <div class="hero__actions">
                <a href="{{ config('site.scipeek_url') }}" class="btn btn--primary btn--lg" rel="noopener">Pasang dari Chrome Web Store</a>
                <a href="#wujud" class="btn btn--outline btn--lg">Lihat wujudnya</a>
            </div>
            <p class="trust-line">Gratis &middot; Offline &middot; Tanpa akun &middot; Chrome</p>
        </div>
    </section>

    {{-- 2. Masalah --}}
    <section class="landing-section">
        <div class="container">
            <h2>Satu jurnal, satu tab baru.</h2>
            <p>Saat menyaring puluhan hasil pencarian, cara yang umum diajarkan adalah membuka Scimago, mengetik nama jurnal, lalu membaca kuartilnya. Satu per satu, untuk setiap hasil.</p>
            <p>SciPeek memindahkan jawabannya ke tempat Anda sudah membaca: di samping nama jurnal itu sendiri.</p>
        </div>
    </section>

    {{-- 3. Wujudnya --}}
    <section class="landing-section landing-section--alt" id="wujud">
        <div class="container">
            <h2>Seperti apa wujudnya?</h2>
            <div class="video video--file">
                <video controls preload="none" playsinline width="1280" height="720"
                       poster="{{ asset('images/landing/scipeek-poster.webp') }}">
                    <source src="{{ asset('videos/landing/scipeek-tutorial.mp4') }}" type="video/mp4">
                </video>
            </div>
            <p class="video-note">Tutorial 40 detik. Teks di dalam video berbahasa Inggris, seperti tampilan ekstensinya.</p>

            <ul class="q-legend" aria-label="Arti warna lencana">
                <li><span class="q q--1">Q1</span> seperempat teratas</li>
                <li><span class="q q--2">Q2</span></li>
                <li><span class="q q--3">Q3</span></li>
                <li><span class="q q--4">Q4</span> seperempat terbawah</li>
            </ul>

            <figure class="figure">
                <img src="{{ asset('images/landing/scipeek-scopus-asli.webp') }}" width="1012" height="580" loading="lazy"
                     alt="Hasil pencarian Scopus dengan lencana kuartil, SJR, dan H-index di samping nama setiap jurnal">
                <figcaption><strong>Scopus.</strong> Lencana di samping jurnal tiap hasil.</figcaption>
            </figure>
            <figure class="figure">
                <img src="{{ asset('images/landing/scipeek-sciencedirect.webp') }}" width="987" height="470" loading="lazy"
                     alt="Hasil pencarian ScienceDirect dengan lencana di atas nama jurnal">
                <figcaption><strong>ScienceDirect.</strong> Lencana di atas nama jurnal.</figcaption>
            </figure>
            <figure class="figure">
                <img src="{{ asset('images/landing/scipeek-scholar.webp') }}" width="639" height="624" loading="lazy"
                     alt="Hasil pencarian Google Scholar dengan lencana di bawah judul, dan tanda strip untuk jurnal yang tidak ditemukan">
                <figcaption><strong>Google Scholar.</strong> Lencana di bawah judul. Strip (&ldquo;&mdash;&rdquo;) berarti jurnalnya tidak ditemukan di Scimago.</figcaption>
            </figure>
            <figure class="figure figure--small">
                <img src="{{ asset('images/landing/scipeek-tab.webp') }}" width="219" height="206" loading="lazy"
                     alt="Kartu kecil di tepi kanan halaman artikel berisi SJR, H-index, dan tombol Open on Scimago">
                <figcaption><strong>Halaman artikel.</strong> Tab di tepi kanan, bisa digeser naik turun. Klik untuk membuka kartu SJR dan H-index.</figcaption>
            </figure>
        </div>
    </section>

    {{-- 4. Cara membaca angka --}}
    <section class="landing-section" id="angka">
        <div class="container">
            <h2>Cara membaca angkanya.</h2>
            <p class="section__intro">Tampilan ekstensi berbahasa Inggris, jadi ini artinya.</p>
            <dl class="facts">
                <dt>Quartile (Q1 sampai Q4)</dt>
                <dd>Posisi jurnal di antara jurnal sebidang menurut SJR. Q1 adalah seperempat teratas. Satu jurnal bisa berbeda kuartil di bidang yang berbeda; ekstensi menampilkan yang terbaik.</dd>
                <dt>SJR</dt>
                <dd>Skor prestise jurnal dari Scimago. Sitasi dari jurnal yang dihormati bobotnya lebih besar. Bandingkan hanya dengan jurnal sebidang.</dd>
                <dt>H-index</dt>
                <dd>Angka h berarti ada h artikel jurnal itu yang masing-masing dikutip sedikitnya h kali. Jurnal yang sudah lama terbit cenderung lebih tinggi.</dd>
            </dl>
            <p class="honest"><strong>Jujur:</strong> angka ini panduan, bukan vonis. Nilai tetap naskahnya, bidangnya, dan kecocokannya dengan lingkup jurnal.</p>
        </div>
    </section>

    {{-- 5. Di mana jalan --}}
    <section class="landing-section landing-section--alt">
        <div class="container">
            <h2>Di mana ia bekerja.</h2>
            <ul class="checklist">
                <li><strong>Lencana di hasil pencarian:</strong> Scopus, ScienceDirect, dan Google Scholar (termasuk daftar publikasi di profil penulis).</li>
                <li><strong>Tab di tepi halaman artikel dan jurnal:</strong> Scopus, ScienceDirect, dan situs penerbit besar lainnya.</li>
            </ul>
            <details class="tool-group">
                <summary>Daftar penerbit</summary>
                <p class="tool-group__body">Springer, Nature, ACS, RSC, Taylor &amp; Francis, Wiley, MDPI, IOP, IEEE Xplore, SAGE, Oxford Academic, Frontiers, Science (AAAS), AIP, APS, Cambridge, De Gruyter, PLOS, dan Cell Press. Tata letak situs penerbit bisa berubah, jadi cakupannya tidak selalu penuh.</p>
            </details>
        </div>
    </section>

    {{-- 6. Privasi --}}
    <section class="landing-section">
        <div class="container">
            <h2>Offline dan tanpa pelacakan.</h2>
            <p>Data jurnal (sekitar 32 ribu judul dari Scimago) ikut terpasang di dalam ekstensi dan dicari di komputer Anda sendiri. Ekstensi tidak mengirim permintaan jaringan, tidak butuh akun, dan tidak mengumpulkan data. Yang disimpan hanya posisi dan status buka-tutup tab, di peramban Anda.</p>
            <p>Dua tautan hanya terbuka bila Anda mengkliknya: halaman jurnal di Scimago, dan kredit &ldquo;Powered by ARTi&rdquo;.</p>
        </div>
    </section>

    {{-- 7. Jujur --}}
    <section class="landing-section landing-section--alt" id="batas">
        <div class="container">
            <h2>Jujur soal batasnya.</h2>
            <ul class="checklist checklist--limits">
                <li><strong>Hanya jurnal yang ada di Scimago.</strong> Jurnal baru, prosiding, buku, tesis, dan preprint tidak diberi peringkat Scimago, jadi tampil &ldquo;&mdash;&rdquo;.</li>
                <li><strong>Bukan pendeteksi jurnal predator,</strong> dan bukan sertifikat &ldquo;terindeks Scopus&rdquo;. Ekstensi hanya menunjukkan apakah jurnal itu ada di data Scimago.</li>
                <li><strong>Google Scholar tidak memberi ISSN,</strong> jadi pencocokan memakai judul jurnal dan bisa meleset pada singkatan yang tidak lazim.</li>
                <li><strong>Datanya snapshot tahunan,</strong> bukan data langsung. Kuartil tahun berjalan bisa berbeda.</li>
                <li><strong>Ini kuartil SJR,</strong> bukan Impact Factor. Impact Factor milik Clarivate dan tidak ditampilkan.</li>
                <li><strong>Hanya Chrome.</strong> Peramban berbasis Chromium lain mungkin bisa memasangnya, tetapi belum saya coba.</li>
                <li>Beberapa penerbit (IEEE, misalnya) kadang memblokir lalu lintas yang tidak biasa, sehingga halamannya sendiri tidak terbuka.</li>
            </ul>
            <p class="muted">ARTi SciPeek proyek independen dan tidak berafiliasi dengan Scimago, Elsevier, Google, atau penerbit mana pun. Data berasal dari Scimago Journal &amp; Country Rank, dipakai non-komersial dan disitasi di setiap kartu.</p>
        </div>
    </section>

    {{-- 8. Pasang --}}
    <section class="landing-section" id="pasang">
        <div class="container">
            <h2>Pasang dalam tiga langkah.</h2>
            <ol class="steps-list">
                <li>Buka halaman ekstensi di Chrome Web Store lalu klik <strong>Add to Chrome</strong>.</li>
                <li>Cari seperti biasa di Scopus, ScienceDirect, atau Google Scholar, atau buka artikel di situs penerbit.</li>
                <li>Baca lencananya, atau klik tab di tepi kanan untuk SJR, H-index, dan tautan ke halaman jurnal di Scimago.</li>
            </ol>
            <div class="hero__actions">
                <a href="{{ config('site.scipeek_url') }}" class="btn btn--primary btn--lg" rel="noopener">Pasang dari Chrome Web Store</a>
            </div>
            <p class="muted">Kode sumbernya terbuka (lisensi MIT) di <a href="https://github.com/okik4zuya/arti-scipeek" rel="noopener">GitHub</a>. Lisensi MIT hanya mencakup kode, bukan data Scimago.</p>
        </div>
    </section>

    {{-- 9. FAQ --}}
    <section class="landing-section landing-section--alt">
        <div class="container container--narrow">
            <h2>Pertanyaan yang sering muncul</h2>
            <div class="faq">
                <details>
                    <summary>Jurnal saya tampil &ldquo;&mdash;&rdquo;. Artinya apa?</summary>
                    <p>Ekstensi tidak menemukan jurnal itu di data Scimago. Bisa karena jurnalnya baru, bukan jurnal (prosiding, buku, preprint), atau judulnya ditulis dengan singkatan yang tidak cocok. Itu bukan penilaian buruk atas jurnalnya.</p>
                </details>
                <details>
                    <summary>Lencananya tidak muncul di halaman saya.</summary>
                    <p>Segarkan halaman setelah memasang. Bila tetap kosong, situs penerbit mungkin mengubah tata letaknya atau tidak menyertakan ISSN di halamannya. Cakupan penerbit tidak pernah penuh.</p>
                </details>
                <details>
                    <summary>Apakah bisa di Edge, Brave, atau Firefox?</summary>
                    <p>Ekstensi dibuat untuk Chrome. Peramban Chromium lain mungkin bisa, tetapi belum saya coba. Firefox tidak didukung.</p>
                </details>
                <details>
                    <summary>Kenapa bukan Impact Factor?</summary>
                    <p>Impact Factor adalah data berlisensi milik Clarivate. SciPeek memakai data Scimago, yang boleh dipakai non-komersial dengan sitasi.</p>
                </details>
                <details>
                    <summary>Apakah ekstensi ini membaca halaman yang saya buka?</summary>
                    <p>Ia berjalan hanya di situs yang tercantum, membaca nama jurnal atau ISSN dari halaman untuk mencari kuartilnya di komputer Anda. Tidak ada yang dikirim ke mana pun. Detailnya ada di kebijakan privasi ekstensi.</p>
                </details>
            </div>
        </div>
    </section>

    {{-- 10. Bagian dari ARTi --}}
    <section class="landing-closing">
        <div class="container container--narrow">
            <h2>Bagian dari ARTi.</h2>
            <p>SciPeek membantu satu langkah kecil, yaitu menilai jurnal. ARTi sendiri adalah framework riset berbahasa Indonesia untuk Claude Code, dari ide sampai submit, dengan memori antar sesi dan referensi yang bisa dilacak.</p>
            <div class="hero__actions hero__actions--center">
                <a href="{{ route('free.start') }}" class="btn btn--outline btn--lg">Baca bab 1 gratis</a>
                <a href="{{ route('fitur') }}" class="btn btn--outline btn--lg">Lihat isi ARTi</a>
            </div>
            <p class="disclaimer">ARTi SciPeek tidak berafiliasi dengan Scimago, Elsevier, Google, atau penerbit mana pun.</p>
        </div>
    </section>

</div>
@endsection
