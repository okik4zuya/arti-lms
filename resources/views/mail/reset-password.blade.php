@extends('mail.transactional')

@section('title', 'Atur ulang password ARTi')

@section('content')
    <p style="margin-top: 0;">Halo,</p>
    <p>Ada permintaan untuk mengatur ulang password akun belajar ARTi dengan alamat email ini. Klik tombol di bawah untuk memilih password baru.</p>
    <p style="margin: 24px 0;">
        <a href="{{ $url }}" style="background: #2563eb; color: #ffffff; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">Atur password</a>
    </p>
    <p style="margin: 0 0 16px; font-size: 14px; color: #6b7280;">Kalau tombol tidak berfungsi, salin tautan ini dan tempel di browser:<br><a href="{{ $url }}" style="word-break: break-all;">{{ $url }}</a></p>
    <p>Tautan ini berlaku {{ $minutes }} menit dan hanya bisa dipakai satu kali. Bila bukan Anda yang meminta, abaikan saja email ini: password Anda tidak berubah.</p>
    <p>ARTi tidak pernah meminta password lewat email atau WhatsApp.</p>
@endsection
