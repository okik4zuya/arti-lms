@extends('mail.layout')

@section('title', $subject)

@section('content')
<p style="margin-top: 0;">Halo,</p>

<p>Ada cerita lama tentang seorang perempuan bernama Rithah. Ia memintal benang seharian, lalu menguraikannya lagi setiap malam. Besoknya mulai dari awal. Kerjanya tidak pernah menumpuk jadi apa-apa.</p>

<p>Bekerja dengan AI yang tidak punya memori terasa persis begitu. Sesi baru berarti mulai dari nol: unggah ulang literatur, jelaskan ulang aturan, ingatkan ulang tujuan. Padahal kemarin sudah. Kerja hari ini tidak pernah jadi pijakan untuk besok.</p>

<p>Fitur memori bawaan di beberapa chat AI membantu sedikit, tapi ada harganya: semua topik, semua sesi, semua konteks bercampur jadi satu memori besar. Kadang ia mengintervensi jawaban yang sama sekali tidak butuh konteks lama. Ini saya bahas di bagian &ldquo;Solusi Parsial&rdquo; di bab 1.</p>

<p><strong>Satu kebiasaan sederhana yang bisa Anda coba:</strong> simpan keputusan proyek (tujuan, aturan penulisan, apa yang sudah diputuskan dan kenapa) di satu berkas catatan per topik. Di awal sesi baru, minta AI membacanya dulu. Itu belum sempurna, tapi sudah jauh lebih baik daripada menjelaskan ulang dari ingatan.</p>

<p>Kisah Rithah lengkapnya ada di bab 1:</p>
<p style="margin: 20px 0;">
    <a href="{{ route('free.show', '04-hilang-memori-tak-berarti') }}" style="background: #2563eb; color: #ffffff; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block;">Baca: Hilang memori, pekerjaan jadi tak berarti</a>
</p>

<p>Beberapa hari lagi saya tunjukkan seperti apa wujud ARTi, supaya Anda bisa menilai sendiri.</p>

<p style="margin-bottom: 0;">Salam,<br>ARTi</p>
@endsection
