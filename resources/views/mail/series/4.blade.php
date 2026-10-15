@extends('mail.layout')

@section('title', $subject)

@section('content')
<p style="margin-top: 0;">Halo,</p>

<p>Ini email terakhir dari seri perkenalan, dan satu-satunya yang berisi tawaran. Kalau ARTi belum cocok untuk Anda, tidak apa-apa: bab 1 dan ARTi PDF tetap gratis.</p>

<p><strong>Isi paket:</strong> e-course ARTi Framework, lisensi ARTi Framework satu tahun (10 skill, dashboard lokal, dan alat pendukung), serta pembaruan selama masa langganan. Bab baru yang terbit selama masa langganan ikut Anda dapat.</p>

@if ($launchActive)
    <p><strong>Harga:</strong> {{ $launchPrice }} per tahun untuk {{ $quota }} pembeli pertama (harga perintis, satu batch saja).@if ($remaining !== null) Saat email ini dikirim, kuota perintis tersisa {{ $remaining }} dari {{ $quota }}.@endif Setelah kuota habis, harganya {{ $normalPrice }} per tahun, sekitar {{ $perMonth }} per bulan. Perpanjangan mengikuti harga normal, kecuali sedang ada promo.</p>
    <p>Harganya lebih rendah sekarang karena e-course masih ditulis. Anda membeli lebih awal, dan bab baru ikut Anda dapat.</p>
@else
    <p><strong>Harga:</strong> {{ $normalPrice }} per tahun, sekitar {{ $perMonth }} per bulan. Harga perintis sudah berakhir. Perpanjangan mengikuti harga normal, kecuali sedang ada promo.</p>
@endif

<p><strong>Garansi {{ $guaranteeDays }} hari.</strong> Tidak puas, uang kembali penuh dalam {{ $guaranteeDays }} hari sejak pembelian, tanpa perlu alasan. Kalau instalasi gagal karena sisi ARTi, uang kembali kapan pun selama masa langganan.</p>

<p><strong>Yang perlu Anda tahu sebelum membeli:</strong></p>
<ul>
    <li>Butuh Claude Code dan langganan Claude berbayar, terpisah dari ARTi.</li>
    <li>Butuh VS Code. Saat ini Windows saja.</li>
    <li>Perlu internet saat instalasi, untuk memeriksa lisensi.</li>
    <li>E-course belum lengkap: bagian Pendahuluan sudah ada, sisanya sedang ditulis.</li>
    <li>ARTi tidak menjamin naskah diterima dan tidak menjanjikan hasil otomatis. Anda tetap yang berpikir dan memeriksa.</li>
</ul>

<p><strong>Cara membeli:</strong> tekan tombol, kirim pesan WhatsApp, lakukan pembayaran, lalu kunci lisensi dan akses e-course dikirim oleh admin.</p>

<p style="margin: 20px 0;">
    <a href="{{ $buyUrl }}" style="background: #2563eb; color: #ffffff; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block;">Beli lewat WhatsApp</a>
</p>

<p>Masih ragu? <a href="{{ route('free.start') }}">Baca bab 1 gratis</a> dulu, atau <a href="{{ rtrim(route('home'), '/') }}/#harga">lihat penawaran lengkapnya</a>.</p>

<p style="margin-bottom: 0;">Terima kasih sudah membaca sampai sini.<br>ARTi</p>
@endsection
