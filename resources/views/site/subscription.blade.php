@extends('layouts.site')

@section('title', $heading)
@section('description', 'Status pendaftaran kabar dari ARTi.')
@push('head')
    <meta name="robots" content="noindex">
@endpush

@section('content')
<div class="container container--narrow">
    <article class="prose">
        <h1>{{ $heading }}</h1>
        <p>{{ $body }}</p>
        <p>
            <a href="{{ route('free.start') }}" class="btn btn--primary">Baca bab 1 gratis</a>
            <a href="{{ route('home') }}" class="btn btn--outline">Ke beranda</a>
        </p>
    </article>
</div>
@endsection
