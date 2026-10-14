@extends('layouts.site')

@section('title', 'Kebijakan privasi')
@section('description', 'Data apa yang disimpan aplikasi ARTi dan untuk apa.')

@section('content')
<div class="container container--narrow">
    <div class="notice">Draf. Teks ini menjelaskan apa yang dilakukan aplikasi hari ini dan belum ditinjau secara hukum.</div>

    <article class="prose">
        <h1>Kebijakan privasi</h1>

        <h2>Data yang disimpan</h2>
        <ul>
            <li>Akun: nama, alamat email, dan sandi (disimpan dalam bentuk hash, bukan teks asli).</li>
            <li>Akses: kursus mana yang dapat Anda buka.</li>
            <li>Kemajuan belajar: subbab yang sudah Anda tandai selesai.</li>
            <li>Sesi: cookie yang menjaga Anda tetap masuk.</li>
        </ul>

        <h2>Yang tidak dilakukan</h2>
        <ul>
            <li>Halaman publik (beranda, fitur, harga, artikel) tidak memasang pelacak iklan.</li>
            <li>Data tidak dijual.</li>
        </ul>

        <h2>Penghapusan</h2>
        <p>Untuk meminta akun dan datanya dihapus, hubungi kami lewat halaman <a href="{{ route('kontak') }}">kontak</a>.</p>
    </article>
</div>
@endsection
