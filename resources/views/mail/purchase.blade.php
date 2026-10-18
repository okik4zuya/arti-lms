@extends('mail.transactional')

@section('title', 'Pembelian ARTi berhasil')

@section('content')
    <p style="margin-top: 0;">Halo {{ $name }},</p>
    <p>Terima kasih sudah membeli ARTi. Pembayaran Anda sudah kami terima. Di bawah ini ada akun belajar, kunci lisensi, dan tautan unduhan.</p>

    <h2 style="font-size: 18px; margin: 24px 0 8px;">1. Akun belajar</h2>
    <p style="margin: 0 0 8px;">Email akun: <strong>{{ $email }}</strong></p>
    @if ($setPasswordUrl)
        <p style="margin: 0 0 8px;">Pilih password Anda sendiri lewat tautan di bawah. Password tidak pernah dikirim lewat email atau WhatsApp.</p>
        <p style="margin: 16px 0;">
            <a href="{{ $setPasswordUrl }}" style="background: #2563eb; color: #ffffff; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">Atur password</a>
        </p>
        <p style="margin: 0 0 8px; font-size: 14px; color: #6b7280;">Kalau tombol tidak berfungsi, salin tautan ini dan tempel di browser:<br><a href="{{ $setPasswordUrl }}" style="word-break: break-all;">{{ $setPasswordUrl }}</a></p>
        <p style="margin: 0; font-size: 14px; color: #6b7280;">Tautan ini tidak ada batas waktunya: Anda bisa memakainya kapan saja. Tautan hanya bisa dipakai satu kali dan tidak berlaku lagi begitu password diatur. Sesudah itu, bila lupa password, pakai <a href="{{ $forgotUrl }}">Lupa password?</a> di halaman masuk.</p>
    @else
        <p style="margin: 0;">Email ini sudah punya akun belajar, jadi password lama Anda tetap berlaku. Masuk di <a href="{{ $loginUrl }}">{{ $loginUrl }}</a>. Lupa password? Pakai <a href="{{ $forgotUrl }}">Lupa password?</a>.</p>
    @endif

    <h2 style="font-size: 18px; margin: 24px 0 8px;">2. Kunci lisensi</h2>
    <p style="margin: 0 0 8px;">{{ $seats }} perangkat, {{ $validity }}.</p>
    <p style="margin: 0 0 8px; font-family: Consolas, 'Courier New', monospace; font-size: 17px; background: #f4f6f8; padding: 10px 12px; border-radius: 6px; word-break: break-all;">{{ $key }}</p>
    <p style="margin: 0; font-size: 14px; color: #6b7280;">Simpan kunci ini dan jangan dibagikan.</p>

    <h2 style="font-size: 18px; margin: 24px 0 8px;">3. Unduh dan pasang</h2>
    <ol style="margin: 0; padding-left: 20px;">
        <li>Masuk ke e-course Anda, lalu buka bagian <strong>Download</strong>: <a href="{{ $downloadPageUrl }}">{{ $downloadPageUrl }}</a></li>
        <li>Unduh installer di halaman itu dan jalankan. Saat diminta, masukkan kunci lisensi di atas.</li>
    </ol>

    <p style="margin: 24px 0 0;">Butuh bantuan? Balas email ini atau hubungi kami lewat halaman <a href="{{ route('kontak') }}">kontak</a>.</p>
@endsection
