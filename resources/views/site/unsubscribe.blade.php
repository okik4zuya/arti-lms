@extends('layouts.site')

@section('title', 'Berhenti berlangganan')
@section('description', 'Berhenti menerima email dari ARTi.')
@push('head')
    <meta name="robots" content="noindex">
@endpush

@section('content')
<div class="container container--narrow">
    <article class="prose">
        @if ($already)
            <h1>Anda sudah berhenti berlangganan</h1>
            <p>Alamat ini tidak menerima email dari ARTi.</p>
        @else
            <h1>Berhenti menerima email dari ARTi?</h1>
            <p>Tekan tombol di bawah untuk berhenti. Setelah itu alamat ini tidak akan dikirimi email lagi.</p>
            <form method="POST" action="{{ $action }}">
                <button type="submit" class="btn btn--primary">Ya, berhenti berlangganan</button>
            </form>
        @endif
        <p><a href="{{ route('home') }}">Ke beranda</a></p>
    </article>
</div>
@endsection
