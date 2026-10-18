@extends('mail.layout')

@section('title', $subject)

@section('content')
<p style="margin-top: 0;">Halo,</p>

<p>Ini email terakhir dari seri perkenalan, dan satu-satunya yang berisi tawaran. Kalau ARTi belum cocok untuk Anda, tidak apa-apa: bab 1 dan ARTi PDF tetap gratis.</p>

<p><strong>Isi paket:</strong> e-course ARTi Framework, lisensi ARTi Framework (10 skill, dashboard lokal, dan alat pendukung), serta pembaruan tanpa biaya tambahan. Bab baru yang terbit ikut Anda dapat.</p>

@if ($promotionLabel)
    <p><strong>Harga:</strong> {{ $promotionLabel }}: {{ $promotionPrice }} (bayar sekali), berlaku sampai {{ $promotionEnds }}. Setelah itu harganya kembali ke {{ $normalPrice }}.</p>
@elseif ($launchActive)
    <p><strong>Harga:</strong> {{ $launchPrice }} bayar sekali untuk {{ $quota }} pembeli pertama (harga perintis, satu batch saja). Saat email ini dikirim, kuota perintis tersisa {{ $remaining }} dari {{ $quota }}. Setelah kuota habis, harganya {{ $normalPrice }}, tetap bayar sekali.</p>
    <p>Harganya lebih rendah sekarang karena e-course masih ditulis. Anda membeli lebih awal, dan bab baru ikut Anda dapat.</p>
@else
    <p><strong>Harga:</strong> {{ $normalPrice }}, bayar sekali. Harga perintis sudah berakhir.</p>
@endif

<p><strong>Yang perlu Anda tahu sebelum membeli:</strong></p>
<ul>
    <li>Butuh Claude Code dan langganan Claude berbayar, terpisah dari ARTi.</li>
    <li>Butuh VS Code. Saat ini Windows saja.</li>
    <li>Perlu internet saat instalasi, untuk memeriksa lisensi.</li>
    <li>E-course belum lengkap: bagian Pendahuluan sudah ada, sisanya sedang ditulis.</li>
    <li>ARTi tidak menjamin naskah diterima dan tidak menjanjikan hasil otomatis. Anda tetap yang berpikir dan memeriksa.</li>
</ul>

<p><strong>Cara membeli:</strong> tekan tombol, isi nama dan email, lalu bayar dengan QRIS, virtual account, atau e-wallet. Akun belajar, kunci lisensi, dan tautan unduhan dikirim ke email Anda.</p>

<p style="margin: 20px 0;">
    <a href="{{ $buyUrl }}" style="background: #2563eb; color: #ffffff; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block;">Beli sekarang</a>
</p>

<p>Masih ragu? <a href="{{ route('free.start') }}">Baca bab 1 gratis</a> dulu, atau <a href="{{ rtrim(route('home'), '/') }}/#harga">lihat penawaran lengkapnya</a>.</p>

<p style="margin-bottom: 0;">Terima kasih sudah membaca sampai sini.<br>ARTi</p>
@endsection
