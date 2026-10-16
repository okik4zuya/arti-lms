@extends('layouts.site')

@section('title', 'Kebijakan privasi')
@section('description', 'Data apa yang disimpan situs dan aplikasi ARTi, apa yang tetap di komputer Anda, dan apa yang dikirim ke layanan lain.')

{{--
    Basis (researched 2026-10-16): UU 27/2022 Pelindungan Data Pribadi (dasar pemrosesan Pasal 20, hak subjek data
    Pasal 5-15, pemberitahuan kegagalan pelindungan Pasal 46, transfer lintas negara Pasal 56). Keep it in step with
    reality:
      - If SITE_DEMO_VIDEO_EMBED_URL is set (an embedded player), the player's host sees visitors: say so below.
      - If the licensing Worker sends more than it does now (key, hashed machine code, OS name, computer name,
        license file, ARTi version, hashed IP for rate limits), say so below.
--}}

@section('content')
<div class="container container--narrow">
    <article class="prose">
        <h1>Kebijakan privasi</h1>
        <p>Kebijakan ini mengikuti UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi. Pengendali data pribadi adalah pengembang ARTi, yang dapat dihubungi lewat halaman <a href="{{ route('kontak') }}">kontak</a>.</p>

        <h2>Data yang disimpan di situs dan aplikasi baca</h2>
        <ul>
            <li>Akun: nama, alamat email, dan sandi (disimpan dalam bentuk hash, bukan teks asli).</li>
            <li>Akses: kursus mana yang dapat Anda buka.</li>
            <li>Kemajuan belajar: subbab yang sudah Anda tandai selesai.</li>
            <li>Cookie: <code>laravel_session</code> menjaga Anda tetap masuk, dan <code>XSRF-TOKEN</code> melindungi formulir dari permintaan palsu. Keduanya dipasang saat Anda membuka situs, juga sebelum masuk, dan bukan pelacak.</li>
        </ul>

        <h2>Pembelian dan lisensi</h2>
        <ul>
            <li>Pembelian berlangsung lewat WhatsApp, jadi admin menerima nomor dan isi pesan Anda. Itu dipakai untuk memproses pembayaran, mengirim kunci lisensi dan akses e-course, dan menangani permintaan uang kembali.</li>
            <li>Nama dan alamat email pembeli disimpan di server lisensi ARTi dan di aplikasi ARTi, untuk menerbitkan dan mengelola lisensi.</li>
            <li>Bukti pembayaran (tangkapan layar atau berkas transfer, yang dapat memuat data rekening) disimpan di penyimpanan privat, hanya dapat dibuka oleh admin dan petugas dukungan ARTi, dan dipakai untuk memeriksa pembayaran.</li>
            <li>Saat aktivasi, ARTi mengirim ke server lisensi ARTi kunci lisensi Anda, kode perangkat yang sudah diacak (hash, bukan ID asli perangkat), nama sistem operasi, dan nama komputer Anda.</li>
            <li>Pengecekan berkala (sekitar tiap beberapa hari, saat dashboard dibuka) mengirim berkas lisensi, kode perangkat yang sudah diacak, dan versi ARTi yang terpasang, untuk memeriksa status lisensi dan pembaruan. Data ini hanya dipakai untuk menjalankan lisensi, memberi dukungan, dan memberi tahu pembaruan.</li>
        </ul>

        <h2>Daftar email (kabar dari ARTi)</h2>
        <ul>
            <li>Bila Anda mengisi formulir &ldquo;kabari saya&rdquo; (di halaman bab 1 gratis atau ARTi PDF), situs menyimpan alamat email Anda, dari halaman mana Anda mendaftar, dan waktu persetujuan serta konfirmasinya.</li>
            <li>ARTi mengirim email konfirmasi lebih dulu. Tanpa konfirmasi, tidak ada email lain yang dikirim.</li>
            <li>Isinya kabar dari ARTi: tulisan baru, pembaruan alat gratis, dan penawaran ARTi. Email dikirim lewat layanan pengiriman email pihak ketiga yang kami pakai untuk itu.</li>
            <li>Setiap email memuat tautan untuk berhenti berlangganan. Untuk menghapus alamat Anda sepenuhnya, hubungi ARTi lewat halaman <a href="{{ route('kontak') }}">kontak</a>.</li>
            <li>Mengunduh ARTi PDF tidak mengharuskan Anda mendaftar. Situs hanya menambah satu hitungan unduhan per hari, tanpa mencatat siapa yang mengunduh.</li>
        </ul>

        <h2>Naskah dan data riset Anda</h2>
        <ul>
            <li>ARTi berjalan di komputer Anda. Berkas proyek, memori, basis data rujukan, dan naskah tersimpan di sana, bukan di server ARTi.</li>
            <li>Isi yang Anda minta Claude proses dikirim ke Anthropic lewat Claude Code, dan tunduk pada kebijakan Anthropic. ARTi tidak berafiliasi dengan Anthropic dan tidak mengendalikan hal itu.</li>
            <li>Pencarian literatur mengirim kata kunci pencarian ke OpenAlex, Semantic Scholar, atau Google Scholar, sesuai layanan yang dipakai.</li>
        </ul>

        <h2>Yang tidak dilakukan</h2>
        <ul>
            <li>Halaman publik (beranda, fitur, bab gratis) tidak memasang pelacak iklan atau analitik pihak ketiga.</li>
            <li>Font dihosting di situs ini sendiri, jadi membuka halaman tidak memanggil Google Fonts atau layanan font lain.</li>
            <li>Data tidak dijual.</li>
        </ul>
        <p>Tautan ke situs lain (YouTube, Chrome Web Store, GitHub) membawa Anda ke layanan yang punya kebijakan privasinya sendiri.</p>

        <h2>Dasar dan tujuan pemrosesan</h2>
        <ul>
            <li>Data akun, pembelian, dan lisensi diproses untuk melaksanakan perjanjian dengan Anda: menerbitkan lisensi, memberi akses e-course, memberi dukungan, dan menangani uang kembali.</li>
            <li>Pengecekan lisensi berkala dan hash alamat IP di server lisensi diproses atas kepentingan sah kami untuk menjaga keamanan dan mencegah penyalahgunaan kunci. Alamat IP tidak disimpan utuh.</li>
            <li>Daftar email diproses atas persetujuan Anda, dan dapat Anda tarik kapan saja.</li>
            <li>Data tidak dipakai untuk tujuan lain tanpa persetujuan Anda, dan tidak dijual.</li>
        </ul>

        <h2>Lama penyimpanan</h2>
        <ul>
            <li>Data akun dan lisensi: selama akun atau lisensi aktif, ditambah 2 tahun setelah berakhir untuk dukungan dan penyelesaian sengketa, lalu dihapus atau dianonimkan.</li>
            <li>Bukti pembayaran dan catatan transaksi: selama kewajiban pembukuan dan perpajakan, umumnya 10 tahun.</li>
            <li>Daftar email: sampai Anda berhenti berlangganan.</li>
        </ul>

        <h2>Hak Anda</h2>
        <p>Anda berhak mengetahui data apa yang kami simpan dan meminta salinannya, memperbaiki data yang keliru, meminta data dihapus atau pemrosesannya dihentikan atau dibatasi, menarik persetujuan, mengajukan keberatan, dan menuntut ganti rugi sesuai UU PDP. Ajukan lewat halaman <a href="{{ route('kontak') }}">kontak</a>. Kami menanggapi tanpa penundaan yang tidak wajar. Data yang wajib kami simpan menurut hukum (misalnya catatan pembayaran) tidak dapat dihapus sebelum jangka waktunya habis.</p>

        <h2>Keamanan dan kebocoran data</h2>
        <p>Akses ke data pembeli dibatasi untuk admin dan petugas dukungan, kunci lisensi disimpan terenkripsi atau sebagai hash, bukti pembayaran disimpan di penyimpanan privat, dan sandi disimpan sebagai hash. Bila terjadi kegagalan pelindungan data pribadi, kami memberi tahu Anda secara tertulis sesuai batas waktu dan ketentuan UU PDP.</p>

        <h2>Penyedia layanan dan transfer ke luar negeri</h2>
        <p>Sebagian layanan dijalankan penyedia di luar negeri, yaitu server lisensi (Cloudflare), pengiriman email, dan Anthropic untuk pemrosesan oleh Claude Code. Kami memakai penyedia yang menerapkan pelindungan data yang memadai, dan hanya mengirim data yang dibutuhkan untuk layanan itu.</p>

        <h2>Anak-anak</h2>
        <p>ARTi ditujukan untuk peneliti dan mahasiswa dewasa. Kami tidak dengan sengaja memproses data anak.</p>

        <h2>Penghapusan</h2>
        <p>Untuk meminta akun dan datanya dihapus, hubungi ARTi lewat halaman <a href="{{ route('kontak') }}">kontak</a>.</p>

        <h2>Perubahan</h2>
        <p>Kebijakan ini dapat diperbarui. Versi terbaru ada di halaman ini, dan perubahan penting kami umumkan lewat email atau pesan WhatsApp.</p>
    </article>
</div>
@endsection
