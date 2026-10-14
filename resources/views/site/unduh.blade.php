@extends('layouts.site')

@section('title', 'Unduh')
@section('description', 'Pemasang ARTi belum dirilis untuk umum. Informasi unduhan akan tersedia di halaman ini.')

@section('content')
<div class="container container--narrow">
    <section class="hero">
        <p class="hero__eyebrow">Unduh</p>
        <h1>Pemasang belum dirilis.</h1>
        <p class="hero__lead">Unduhan ARTi belum tersedia untuk umum. Tautan dan petunjuk pemasangan akan muncul di halaman ini saat rilis.</p>
    </section>

    <div class="panel">
        <h3>Sementara menunggu</h3>
        <ul>
            <li>Baca <a href="{{ route('fitur') }}">fitur</a> untuk tahu apa yang akan Anda dapat.</li>
            <li>Lihat <a href="{{ route('harga') }}">rencana lisensi</a>.</li>
            <li>Ikuti perkembangan lewat <a href="{{ route('articles.index') }}">artikel</a> atau <a href="{{ route('feed') }}">umpan</a>.</li>
        </ul>
    </div>
</div>
@endsection
