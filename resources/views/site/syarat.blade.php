@extends('layouts.site')

@inject('offer', 'App\Support\Offer')

@section('title', 'Syarat penggunaan')
@section('description', 'Syarat penggunaan situs, e-course, dan lisensi ARTi: paket, harga, garansi uang kembali 7 hari, dan batas tanggung jawab.')

{{--
    Basis (researched 2026-10-16): UU 8/1999 Perlindungan Konsumen (Pasal 18: no standard clause that shifts the
    seller's responsibility or binds the buyer to rules changed unilaterally; Pasal 18(3): such a clause is void),
    PP 80/2019 PMSE (identity and contract information for electronic trade), UU 28/2014 Hak Cipta (software is
    licensed, not sold), UU 11/2008 ITE (electronic contracts). Keep these constraints when editing.
--}}

@section('content')
<div class="container container--narrow">
    <article class="prose">
        <h1>Syarat penggunaan</h1>
        <p>Halaman ini mengatur pembelian dan penggunaan paket ARTi. Perjanjian lisensi perangkat lunak (EULA) ditampilkan saat pemasangan dan melengkapi halaman ini. Bila keduanya berbeda, yang lebih menguntungkan Anda sebagai konsumen yang berlaku.</p>

        <h2>Penyelenggara</h2>
        <p>ARTi dijual dan dikelola oleh pengembangnya. Identitas dan saluran resmi untuk bertanya, membeli, mengajukan keluhan, atau meminta uang kembali ada di halaman <a href="{{ route('kontak') }}">kontak</a>. Pembelian hanya sah bila dilakukan lewat saluran itu.</p>

        <h2>Akun</h2>
        <p>Akun dibuat oleh admin setelah pembayaran. Jaga kerahasiaan sandi Anda dan jangan membagikan akses ke orang lain.</p>

        <h2>Paket, harga, dan perpanjangan</h2>
        <p>Paket terdiri atas e-course ARTi Framework dan lisensi ARTi Framework untuk satu tahun, dihitung sejak kunci lisensi diterbitkan. Harga normal {{ $offer->format($offer->normalPrice()) }} per tahun. Harga perintis {{ $offer->format($offer->launchPrice()) }} berlaku untuk {{ $offer->quota() }} pembeli pertama dan hanya satu gelombang. Perpanjangan mengikuti harga normal, kecuali sedang ada promo. Harga yang berlaku adalah harga yang disebutkan kepada Anda saat Anda membayar.</p>
        <p>Langganan Claude dan Claude Code dibayar terpisah ke Anthropic dan tunduk pada syarat Anthropic. ARTi tidak berafiliasi dengan Anthropic.</p>

        <h2>Garansi uang kembali</h2>
        <p>Tidak puas, uang kembali penuh dalam {{ $offer->guaranteeDays() }} hari sejak tanggal pembelian, tanpa perlu alasan. Ajukan lewat WhatsApp admin. Setelah itu kunci lisensi dicabut dan akses e-course dihapus.</p>
        <p>Kalau instalasi gagal karena sisi ARTi, uang kembali kapan pun selama masa langganan. Hak Anda menurut UU Perlindungan Konsumen tetap berlaku di luar garansi ini.</p>

        <h2>Lisensi, perangkat, dan pemindahan</h2>
        <p>Anda memperoleh hak menggunakan ARTi, bukan memilikinya. Satu lisensi berlaku untuk satu pengguna pada satu komputer pada satu waktu, dan tidak boleh dibagikan atau dijual kembali. Hak cipta atas ARTi tetap pada pengembangnya.</p>
        <p>Untuk pindah komputer, nonaktifkan lisensi di komputer lama (Pengaturan &gt; License di dashboard), lalu aktifkan di komputer baru. Bila komputer lama tidak dapat dipakai lagi, hubungi admin lewat WhatsApp. Penonaktifan mandiri dibatasi 3 kali per tahun. Seat yang dipegang komputer lain hanya dapat dipindahkan oleh admin, supaya kunci tidak dapat dilepas oleh pihak yang tidak berhak.</p>

        <h2>Masa berlaku</h2>
        <p>Setelah lisensi berakhir (dan masa tenggang 14 hari lewat), pembaruan ARTi berhenti sampai lisensi diperpanjang. Dashboard tetap bisa dibuka, dan skill, alat, serta data proyek Anda tidak disentuh. Berkas dan data proyek Anda tetap milik Anda.</p>

        <h2>Pencabutan lisensi</h2>
        <p>Lisensi dapat dicabut bila Anda meminta uang kembali, atau bila kunci dibagikan atau dijual kembali, atau bila syarat ini dilanggar. Kami memberi tahu Anda lebih dulu bila memungkinkan. Bila lisensi dicabut, dashboard ARTi berhenti dapat dipakai pada pengecekan online berikutnya, biasanya dalam satu sampai tiga hari saat komputer terhubung internet. Skill, alat, dan data penelitian Anda tidak dihapus dan tetap dapat dipakai lewat Claude Code di VS Code. Bila Anda menilai pencabutan keliru, hubungi kami lewat halaman kontak.</p>

        <h2>Isi e-course</h2>
        <p>Isi e-course dan materi kursus dilindungi hak cipta. Anda boleh membacanya untuk keperluan pribadi. Menyalin atau menyebarkannya ulang tanpa izin tidak diperbolehkan.</p>
        <p>E-course belum lengkap. Bab baru yang terbit selama masa langganan Anda ikut Anda dapat.</p>

        <h2>Tanggung jawab Anda atas naskah</h2>
        <p>ARTi membantu prosesnya dan tidak menjamin naskah diterima jurnal. ARTi tidak menjanjikan naskah lolos detektor AI. Anda yang menentukan ide dan kesimpulan, memeriksa setiap rujukan, dan mengikuti kebijakan penggunaan AI jurnal tujuan Anda.</p>

        <h2>Ketersediaan dan batas tanggung jawab</h2>
        <p>Isi dan fitur dapat berubah. ARTi bergantung pada Claude Code, jadi perubahan di sisi Claude dapat memengaruhi cara kerjanya. Saat ini ARTi hanya mendukung Windows. ARTi tidak bertanggung jawab atas keputusan jurnal, kebijakan jurnal tentang penggunaan AI, atau perubahan layanan pihak ketiga yang di luar kendalinya. Untuk kerugian yang timbul dari kesalahan ARTi sendiri, ganti rugi mengikuti UU Perlindungan Konsumen, dan tidak ada ketentuan di sini yang meniadakannya.</p>

        <h2>Data pribadi</h2>
        <p>Cara kami mengelola data Anda dijelaskan di <a href="{{ route('privasi') }}">kebijakan privasi</a>.</p>

        <h2>Perubahan syarat</h2>
        <p>Syarat ini dapat diperbarui. Versi terbaru ada di halaman ini, dan perubahan penting kami umumkan lewat email atau pesan WhatsApp. Perubahan yang merugikan Anda tidak berlaku untuk masa langganan yang sedang berjalan tanpa persetujuan Anda. Perubahan itu baru berlaku pada pembelian atau perpanjangan berikutnya.</p>

        <h2>Hukum yang berlaku dan sengketa</h2>
        <p>Syarat ini tunduk pada hukum Republik Indonesia. Keluhan dan sengketa diselesaikan lebih dulu secara musyawarah lewat halaman kontak. Bila tidak selesai, Anda dapat mengajukannya ke Badan Penyelesaian Sengketa Konsumen (BPSK) atau ke pengadilan negeri di tempat Anda berdomisili, sesuai UU Perlindungan Konsumen.</p>
    </article>
    </article>
</div>
@endsection
