@extends('mail.layout')

@section('title', $subject)

@section('content')
<p style="margin-top: 0;">Halo,</p>

<p>Dua email terakhir membahas dua masalah: AI yang mengarang dan AI yang lupa. Ini jawaban ARTi, tanpa istilah rumit.</p>

<p>ARTi bukan alat baru. Ia cara kerja riset yang berjalan di atas Claude Code. Claude sudah pintar, tapi belum tentu tahu cara meneliti dengan benar. ARTi memberinya <strong>skill</strong> (lembar langkah riset, dari mencari gap sampai membalas reviewer) dan <strong>alat</strong> (mencari literatur, mengubah PDF, menyiapkan naskah dan gambar). Di atasnya ada tiga fondasi:</p>

<ul>
    <li><strong>Memori per topik.</strong> Keputusan dan konteks proyek tersimpan sebagai berkas kecil per topik, dengan indeks. Sesi berikutnya melanjutkan, bukan mengulang.</li>
    <li><strong>Referensi asli dan bisa dilacak.</strong> Satu basis data rujukan per proyek. Setiap kutipan menunjuk ke sumber yang benar-benar ada.</li>
    <li><strong>Scratchbook.</strong> Tempat menimbun temuan dan kutipan per bagian naskah, dengan penanda asal setiap klaim. Klaim tanpa sumber tidak masuk naskah.</li>
</ul>

<p>Semuanya berkas biasa di komputer Anda, bukan kotak hitam.</p>

<p style="margin: 20px 0;">
    <a href="{{ route('fitur') }}" style="background: #2563eb; color: #ffffff; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block;">Lihat isi ARTi</a>
</p>

<p><strong>Jujur soal syaratnya:</strong> ARTi butuh Claude Code dan langganan Claude berbayar (dibayar terpisah dari ARTi), VS Code, dan saat ini hanya untuk Windows. E-course masih ditulis: bagian Pendahuluan sudah ada, sisanya menyusul. ARTi membantu prosesnya, tetapi diterima atau tidaknya naskah tetap keputusan jurnal.</p>

<p style="margin-bottom: 0;">Salam,<br>ARTi</p>
@endsection
