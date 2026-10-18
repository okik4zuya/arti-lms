@extends('layouts.site')

@inject('offer', 'App\Support\Offer')
@inject('gateways', 'App\Services\Payments\PaymentGateways')

@section('title', 'Syarat penggunaan')
@section('description', 'Syarat penggunaan situs, e-course, dan lisensi ARTi: paket sekali bayar, pembayaran, pengembalian dana, dan batas tanggung jawab.')

{{--
    Basis (researched 2026-10-16, refund and lifetime parts re-researched 2026-10-18):
    - UU 8/1999 Perlindungan Konsumen. Pasal 18(1): a standard clause may not shift the seller's responsibility, may not
      let the seller refuse to take goods back or refuse to return money paid for goods or services (huruf b and c),
      and may not bind the buyer to rules changed unilaterally; Pasal 18(3): such a clause is void. So this page can NOT say
      "no refunds". Pasal 4 huruf h: a consumer is entitled to compensation or replacement when goods or services are not
      as agreed. The refund section below therefore lists when money is returned and keeps the consumer's statutory rights.
    - PP 80/2019 PMSE (identity and contract information for electronic trade, cancellation and exchange rules),
      UU 28/2014 Hak Cipta (software is licensed, not sold), UU 11/2008 ITE (electronic contracts).
    - Decided 2026-10-18 by the owner: one-time purchase, "lifetime" wording kept light; the 7-day money-back promise was
      removed from the marketing pages, the refund mechanism stays in the system.
    NOT reviewed by a lawyer. Points a lawyer should look at: the lifetime and service-ending clause, the refund conditions
    and their time limits, the clause on future major versions, and whether the seller needs a business licence or
    registration (PP 80/2019 Pasal 15, 21) given the seller is an individual.
--}}

@section('content')
<div class="container container--narrow">
    <article class="prose">
        <h1>Syarat penggunaan</h1>
        <p>Halaman ini mengatur pembelian dan penggunaan paket ARTi. Perjanjian lisensi perangkat lunak (EULA) ditampilkan saat pemasangan dan melengkapi halaman ini. Bila keduanya berbeda, yang lebih menguntungkan Anda sebagai konsumen yang berlaku.</p>

        <h2>Penyelenggara</h2>
        <p>ARTi dijual dan dikelola oleh pengembangnya. Identitas dan saluran resmi untuk bertanya, membeli, mengajukan keluhan, atau meminta pengembalian dana ada di halaman <a href="{{ route('kontak') }}">kontak</a>. Pembelian hanya sah bila dilakukan lewat situs ini atau saluran di halaman itu.</p>

        <h2>Akun</h2>
        <p>Akun belajar dibuat otomatis setelah pembayaran Anda diterima (atau oleh admin, bila Anda membeli lewat WhatsApp). Anda memilih password sendiri lewat tautan sekali pakai yang dikirim ke email Anda. ARTi tidak pernah mengirim atau meminta password lewat email, WhatsApp, atau pesan lain.</p>
        <p>Tautan pertama itu tidak ada batas waktunya, jadi Anda boleh memakainya kapan saja. Tautan hanya bisa dipakai satu kali dan tidak berlaku lagi begitu password diatur atau diganti. Sesudah itu, bila lupa password, pakai &ldquo;Lupa password?&rdquo; di halaman masuk. Jaga kerahasiaan password dan tautan Anda, dan jangan membagikan akses ke orang lain.</p>

        <h2>Paket, harga, dan pembayaran</h2>
        <p>Paket terdiri atas e-course ARTi Framework dan lisensi ARTi Framework. Anda membayar sekali, tanpa biaya tahunan. Harga normal {{ $offer->format($offer->normalPrice()) }}. Harga perintis {{ $offer->format($offer->launchPrice()) }} berlaku untuk {{ $offer->quota() }} pembeli pertama dan hanya satu gelombang. ARTi dapat mengadakan promo untuk acara tertentu. Promo selalu diberi nama sendiri, tidak disebut harga perintis, dan tanggal berakhirnya tertera di halaman penawaran.</p>
        <p>Harga yang berlaku untuk Anda adalah harga yang tertera di layar pemesanan saat Anda memesan, dan tidak berubah sesudahnya. Harga dalam rupiah.</p>
        <p>Pembayaran lewat QRIS, virtual account, atau e-wallet diproses oleh {{ $gateways->current()->label() }}. ARTi tidak menyimpan nomor kartu atau rekening Anda. Pembelian lewat WhatsApp juga tersedia sebagai cadangan. Pesanan yang belum dibayar kedaluwarsa dalam {{ (int) config('site.offer.payment_window_minutes') }} menit, dan tempat harga perintis yang ditahan selama itu dilepas kembali. Bila Anda membayar setelah pesanan kedaluwarsa, pembayaran tetap kami proses, atau kami kembalikan bila harganya sudah tidak tersedia dan Anda tidak setuju dengan harga yang berlaku.</p>
        <p>Setelah pembayaran terkonfirmasi, akun, kunci lisensi, dan tautan unduhan dikirim ke email Anda, biasanya dalam beberapa menit. Bila tidak sampai dalam satu jam, periksa folder spam lalu hubungi kami.</p>
        <p>Langganan Claude dan Claude Code dibayar terpisah ke Anthropic dan tunduk pada syarat Anthropic. ARTi tidak berafiliasi dengan Anthropic.</p>

        <h2 id="masa-berlaku">Masa berlaku dan pembaruan</h2>
        <p>Lisensi tidak punya tanggal kedaluwarsa. Kata &ldquo;selamanya&rdquo; atau &ldquo;lifetime&rdquo; di situs dan di aplikasi berarti lisensi berlaku selama ARTi tersedia, bukan seumur hidup Anda dan bukan tanpa batas bagi ARTi.</p>
        <p>Pembaruan ARTi dan bab baru e-course yang terbit untuk versi yang Anda beli ikut Anda dapat tanpa biaya tambahan, selama ARTi dikembangkan. ARTi tidak menjanjikan jadwal atau fitur tertentu. Versi besar yang berbeda secara mendasar dapat ditawarkan sebagai produk terpisah. Bila itu terjadi, lisensi Anda tetap berlaku untuk versi yang Anda beli beserta pembaruannya.</p>
        <p>Bila ARTi suatu hari dihentikan secara permanen, kami mengumumkannya lewat email dan situs paling lambat 90 hari sebelumnya, dan selama masa itu aktivasi serta pembaruan tetap berjalan. Setelahnya, layanan online (aktivasi baru, pemindahan perangkat, pembaruan) dapat berhenti. Kami berusaha agar komputer yang sudah diaktifkan tetap dapat dipakai, tetapi tidak dapat menjamin kerja ARTi bila layanan pihak ketiga yang dipakainya berubah. Berkas dan data proyek Anda tetap milik Anda dan tidak disentuh.</p>

        <h2 id="pengembalian">Pengembalian dana</h2>
        <p>ARTi tidak menawarkan garansi uang kembali tanpa alasan. Uang Anda dikembalikan penuh dalam keadaan berikut:</p>
        <ul>
            <li>Anda sudah membayar, tetapi akun atau kunci lisensi tidak Anda terima dan kami tidak berhasil memperbaikinya dalam 3 hari sejak Anda melapor.</li>
            <li>Pembayaran terjadi dua kali untuk pesanan yang sama, atau jumlah yang Anda bayar lebih besar dari harga yang berlaku saat Anda memesan (selisihnya dikembalikan).</li>
            <li>ARTi tidak dapat dipasang atau diaktifkan di komputer Windows yang memenuhi syarat karena sisi ARTi, dan kami tidak berhasil memperbaikinya dalam 14 hari sejak Anda melapor, dengan laporan dalam 30 hari sejak pembelian.</li>
            <li>Isi paket secara nyata berbeda dari yang dijanjikan di halaman penawaran saat Anda membeli.</li>
        </ul>
        <p>Di luar keadaan itu, permintaan pengembalian dana kami nilai satu per satu secara wajar, tanpa janji. Hak Anda menurut UU Perlindungan Konsumen, termasuk hak atas penggantian bila barang atau jasa tidak sesuai dengan perjanjian, tetap berlaku dan tidak dibatasi oleh halaman ini.</p>
        <p>Ajukan lewat halaman <a href="{{ route('kontak') }}">kontak</a> dengan menyebut email pembelian Anda. Kami menanggapi dalam 3 hari kerja. Bila disetujui, dana dikembalikan lewat {{ $gateways->current()->label() }} atau saluran pembayaran yang sama dan umumnya sampai dalam 14 hari kerja, tergantung bank atau penyedia e-wallet Anda. Sesudah itu kunci lisensi dicabut dan akses e-course dihapus.</p>

        <h2>Lisensi, perangkat, dan pemindahan</h2>
        <p>Anda memperoleh hak menggunakan ARTi, bukan memilikinya. Satu lisensi berlaku untuk satu pengguna pada satu komputer pada satu waktu, dan tidak boleh dibagikan atau dijual kembali. Hak cipta atas ARTi tetap pada pengembangnya.</p>
        <p>Untuk pindah komputer, nonaktifkan lisensi di komputer lama (Pengaturan &gt; License di dashboard), lalu aktifkan di komputer baru. Bila komputer lama tidak dapat dipakai lagi, hubungi admin lewat halaman kontak. Penonaktifan mandiri dibatasi 3 kali per tahun. Seat yang dipegang komputer lain hanya dapat dipindahkan oleh admin, supaya kunci tidak dapat dilepas oleh pihak yang tidak berhak.</p>

        <h2>Pencabutan lisensi</h2>
        <p>Lisensi dapat dicabut bila dana Anda dikembalikan, bila pembayaran dibatalkan atau ditarik kembali, bila kunci dibagikan atau dijual kembali, atau bila syarat ini dilanggar. Kami memberi tahu Anda lebih dulu bila memungkinkan. Bila lisensi dicabut, dashboard ARTi berhenti dapat dipakai pada pengecekan online berikutnya, biasanya dalam satu sampai tiga hari saat komputer terhubung internet. Skill, alat, dan data penelitian Anda tidak dihapus dan tetap dapat dipakai lewat Claude Code di VS Code. Bila Anda menilai pencabutan keliru, hubungi kami lewat halaman kontak.</p>

        <h2>Isi e-course</h2>
        <p>Isi e-course dan materi kursus dilindungi hak cipta. Anda boleh membacanya untuk keperluan pribadi. Menyalin atau menyebarkannya ulang tanpa izin tidak diperbolehkan.</p>
        <p>E-course belum lengkap. Bab baru yang terbit ikut Anda dapat tanpa biaya tambahan.</p>

        <h2>Tanggung jawab Anda atas naskah</h2>
        <p>ARTi membantu prosesnya dan tidak menjamin naskah diterima jurnal. ARTi tidak menjanjikan naskah lolos detektor AI. Anda yang menentukan ide dan kesimpulan, memeriksa setiap rujukan, dan mengikuti kebijakan penggunaan AI jurnal tujuan Anda.</p>

        <h2>Ketersediaan dan batas tanggung jawab</h2>
        <p>Isi dan fitur dapat berubah. ARTi bergantung pada Claude Code, jadi perubahan di sisi Claude dapat memengaruhi cara kerjanya. Saat ini ARTi hanya mendukung Windows. ARTi tidak bertanggung jawab atas keputusan jurnal, kebijakan jurnal tentang penggunaan AI, atau perubahan layanan pihak ketiga yang di luar kendalinya. Untuk kerugian yang timbul dari kesalahan ARTi sendiri, ganti rugi mengikuti UU Perlindungan Konsumen, dan tidak ada ketentuan di sini yang meniadakannya.</p>

        <h2>Data pribadi</h2>
        <p>Cara kami mengelola data Anda dijelaskan di <a href="{{ route('privasi') }}">kebijakan privasi</a>.</p>

        <h2>Perubahan syarat</h2>
        <p>Syarat ini dapat diperbarui. Versi terbaru ada di halaman ini, dan perubahan penting kami umumkan lewat email atau pesan WhatsApp. Perubahan yang merugikan Anda tidak berlaku untuk lisensi yang sudah Anda beli tanpa persetujuan Anda, dan baru berlaku pada pembelian berikutnya.</p>

        <h2>Hukum yang berlaku dan sengketa</h2>
        <p>Syarat ini tunduk pada hukum Republik Indonesia. Keluhan dan sengketa diselesaikan lebih dulu secara musyawarah lewat halaman kontak. Bila tidak selesai, Anda dapat mengajukannya ke Badan Penyelesaian Sengketa Konsumen (BPSK) atau ke pengadilan negeri di tempat Anda berdomisili, sesuai UU Perlindungan Konsumen.</p>
    </article>
</div>
@endsection
