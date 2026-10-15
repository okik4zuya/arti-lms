@extends('layouts.site')

@section('title', 'Kebijakan privasi')
@section('description', 'Data apa yang disimpan situs dan aplikasi ARTi, apa yang tetap di komputer Anda, dan apa yang dikirim ke layanan lain.')

{{--
    LEGAL REVIEW (see memory/memories/legal-review-notes.md): this page states what the system does today.
    Keep it in step with reality:
      - If SITE_DEMO_VIDEO_EMBED_URL is set (an embedded player), the player's host sees visitors: say so below.
      - If the licensing Worker sends more than the licence key and a hashed machine code, say so below.
--}}

@section('content')
<div class="container container--narrow">
    <div class="notice">Draf. Teks ini menjelaskan apa yang dilakukan situs dan aplikasi hari ini dan belum ditinjau secara hukum.</div>

    <article class="prose">
        <h1>Kebijakan privasi</h1>

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
            <li>Saat instalasi, ARTi memeriksa lisensi lewat internet ke server lisensi ARTi. Yang dikirim adalah kunci lisensi dan kode perangkat yang sudah diacak (hash), bukan data mentah komputer Anda.</li>
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

        <h2>Penghapusan</h2>
        <p>Untuk meminta akun dan datanya dihapus, hubungi ARTi lewat halaman <a href="{{ route('kontak') }}">kontak</a>.</p>
    </article>
</div>
@endsection
