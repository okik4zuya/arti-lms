@extends('mail.layout')

@section('title', $subject)

@section('content')
<p style="margin-top: 0;">Halo,</p>

<p>Anda baru-baru ini meminta kabar dari ARTi. Satu keluhan yang paling sering saya dengar dari dosen: &ldquo;AI-nya yakin sekali, tapi referensinya tidak ada.&rdquo;</p>

<p>Itu yang disebut halusinasi. Model bahasa menyusun kalimat yang terdengar masuk akal, termasuk kutipan yang tampak meyakinkan, padahal tidak ada di jurnal yang disebut. Ia tidak membuka arsip. Ia merangkai.</p>

<p>Masalahnya lebih besar dari satu kalimat yang keliru. Kalimat itu masuk ke artikel tanpa dicek ke sumber aslinya. Artikel terbit, orang lain merujuknya, lalu artikel yang merujuk itu dirujuk lagi. Lama-lama kesalahan itu dianggap benar, bukan karena benar, tapi karena terus disebarkan.</p>

<p><strong>Tiga kebiasaan yang bisa Anda mulai hari ini, tanpa alat apa pun:</strong></p>
<ul>
    <li>Jangan menerima satu kutipan pun dari AI sebelum Anda membuka sumbernya sendiri.</li>
    <li>Minta AI menyebut DOI, lalu periksa DOI itu di sumber resminya. DOI yang dikarang biasanya tidak ditemukan.</li>
    <li>Catat dari mana setiap klaim berasal, sejak awal, bukan setelah naskah selesai.</li>
</ul>

<p>Bagian bab 1 yang membahas ini lebih panjang, dengan contohnya:</p>
<p style="margin: 20px 0;">
    <a href="{{ route('free.show', '03-halusinasi-meruntuhkan-kredibilitas-akademik') }}" style="background: #2563eb; color: #ffffff; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block;">Baca: Halusinasi dan kredibilitas akademik</a>
</p>

<p>Besok-lusa saya cerita soal masalah kedua: kerja yang hilang setiap sesi baru.</p>

<p style="margin-bottom: 0;">Salam,<br>ARTi</p>
@endsection
