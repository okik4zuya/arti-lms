@extends('layouts.site')

@section('title', $article->title)
@section('description', $article->description)
@section('og_type', 'article')

@push('head')
    <meta property="article:published_time" content="{{ $article->date->toAtomString() }}">
@endpush

@section('content')
<div class="container container--narrow">
    <article class="prose">
        <h1>{{ $article->title }}</h1>
        <p class="article-meta">
            <time datetime="{{ $article->date->toDateString() }}">{{ $article->date->locale('id')->translatedFormat('j F Y') }}</time>
        </p>

        {!! $html !!}
    </article>

    @if ($article->tags)
        <ul class="tags">
            @foreach ($article->tags as $tag)
                <li><a class="tag" href="{{ route('articles.tag', $tag) }}">{{ $tag }}</a></li>
            @endforeach
        </ul>
    @endif

    <p style="margin-top: var(--space-6);"><a href="{{ route('articles.index') }}">&larr; Semua artikel</a></p>
</div>
@endsection
