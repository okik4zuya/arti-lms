@extends('layouts.site')

@inject('offer', 'App\Support\Offer')

@section('title', 'Syarat penggunaan')
@section('description', 'Syarat penggunaan situs, e-course, dan lisensi ARTi: paket, harga, garansi uang kembali 7 hari, dan batas tanggung jawab.')

{{--
    LEGAL REVIEW (see memory/memories/legal-review-notes.md): every line here restates a decision already
    published on the landing page. Clauses a lawyer should add or check (liability limit, governing law,
    licence scope, device limit, termination) are proposed in that memory file, not invented here.
--}}

@section('content')
<div class="container container--narrow">
    <div class="notice">Draf. Syarat lisensi perangkat lunak (EULA) terpisah dan belum diterbitkan, dan teks ini belum ditinjau secara hukum.</div>

    <article class="prose">
        <h1>Syarat penggunaan</h1>

        <h2>Akun</h2>
        <p>Akun dibuat oleh admin. Jaga kerahasiaan sandi Anda dan jangan membagikan akses ke orang lain.</p>

        <h2>Paket, harga, dan perpanjangan</h2>
        <p>Paket terdiri atas e-course ARTi Framework dan lisensi ARTi Framework untuk satu tahun. Harga normal {{ $offer->format($offer->normalPrice()) }} per tahun. Harga perintis {{ $offer->format($offer->launchPrice()) }} berlaku untuk {{ $offer->quota() }} pembeli pertama dan hanya satu gelombang. Perpanjangan mengikuti harga normal, kecuali sedang ada promo.</p>
        <p>Langganan Claude dan Claude Code dibayar terpisah ke Anthropic dan tunduk pada syarat Anthropic. ARTi tidak berafiliasi dengan Anthropic.</p>

        <h2>Garansi uang kembali</h2>
        <p>Tidak puas, uang kembali penuh dalam {{ $offer->guaranteeDays() }} hari sejak tanggal pembelian, tanpa perlu alasan. Ajukan lewat WhatsApp admin. Setelah itu kunci lisensi dicabut dan akses e-course dihapus.</p>
        <p>Kalau instalasi gagal karena sisi ARTi, uang kembali kapan pun selama masa langganan.</p>

        <h2>Masa berlaku</h2>
        <p>Setelah lisensi berakhir, dashboard ARTi tidak bisa dibuka sampai lisensi diperpanjang. Berkas dan data proyek Anda tetap milik Anda.</p>

        <h2>Isi e-course</h2>
        <p>Isi e-course dan materi kursus dilindungi hak cipta. Anda boleh membacanya untuk keperluan pribadi. Menyalin atau menyebarkannya ulang tanpa izin tidak diperbolehkan.</p>
        <p>E-course belum lengkap. Bab baru yang terbit selama masa langganan Anda ikut Anda dapat.</p>

        <h2>Tanggung jawab Anda atas naskah</h2>
        <p>ARTi membantu prosesnya dan tidak menjamin naskah diterima jurnal. ARTi tidak menjanjikan naskah lolos detektor AI. Anda yang menentukan ide dan kesimpulan, memeriksa setiap rujukan, dan mengikuti kebijakan penggunaan AI jurnal tujuan Anda.</p>

        <h2>Ketersediaan</h2>
        <p>Layanan diberikan apa adanya. Isi dan fitur dapat berubah. ARTi bergantung pada Claude Code, jadi perubahan di sisi Claude dapat memengaruhi cara kerjanya. Saat ini ARTi hanya mendukung Windows.</p>

        <h2>Perubahan</h2>
        <p>Syarat ini dapat diperbarui. Versi terbaru selalu ada di halaman ini.</p>
    </article>
</div>
@endsection
