@extends('layouts.site')

@section('title', isset($tag) ? "Artikel bertanda {$tag}" : 'Artikel')
@section('description', isset($tag) ? "Artikel ARTi dengan tanda {$tag}." : 'Catatan dan tulisan dari proses membangun ARTi Framework.')

@if ($articles->isEmpty())
    @push('head')
        <meta name="robots" content="noindex">
    @endpush
@endif

@section('content')
<div class="container container--narrow">
    <section class="hero">
        <p class="hero__eyebrow">Artikel</p>
        <h1>{{ isset($tag) ? "Tanda: {$tag}" : 'Tulisan dari proses membangun ARTi.' }}</h1>
        @isset($tag)
            <p><a href="{{ route('articles.index') }}">&larr; Semua artikel</a></p>
        @endisset
    </section>

    @if ($articles->isEmpty())
        <div class="panel panel--muted">
            <p>Belum ada artikel.</p>
        </div>
    @else
        <ul class="article-list">
            @foreach ($articles as $article)
                <li class="article-list__item">
                    <h2 class="article-list__title"><a href="{{ route('articles.show', $article->slug) }}">{{ $article->title }}</a></h2>
                    <p class="article-meta" style="margin: 0;">{{ $article->date->locale('id')->translatedFormat('j F Y') }}</p>
                    <p class="article-list__desc">{{ $article->description }}</p>
                    @if ($article->tags)
                        <ul class="tags">
                            @foreach ($article->tags as $articleTag)
                                <li><a class="tag" href="{{ route('articles.tag', $articleTag) }}">{{ $articleTag }}</a></li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>

        @unless (isset($tag) || $tags->isEmpty())
            <section class="section">
                <h2>Tanda</h2>
                <ul class="tags">
                    @foreach ($tags as $name => $count)
                        <li><a class="tag" href="{{ route('articles.tag', $name) }}">{{ $name }} ({{ $count }})</a></li>
                    @endforeach
                </ul>
            </section>
        @endunless
    @endif
</div>
@endsection
