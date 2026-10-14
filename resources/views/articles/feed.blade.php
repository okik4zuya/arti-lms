{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<feed xmlns="http://www.w3.org/2005/Atom" xml:lang="id">
    <title>Artikel ARTi</title>
    <id>{{ route('feed') }}</id>
    <link rel="self" type="application/atom+xml" href="{{ route('feed') }}"/>
    <link rel="alternate" type="text/html" href="{{ route('articles.index') }}"/>
    <updated>{{ ($articles->first()?->date ?? now())->toAtomString() }}</updated>
@foreach ($articles as $article)
    <entry>
        <title>{{ $article->title }}</title>
        <id>{{ route('articles.show', $article->slug) }}</id>
        <link rel="alternate" type="text/html" href="{{ route('articles.show', $article->slug) }}"/>
        <updated>{{ $article->date->toAtomString() }}</updated>
        <summary>{{ $article->description }}</summary>
@foreach ($article->tags as $tag)
        <category term="{{ $tag }}"/>
@endforeach
    </entry>
@endforeach
</feed>
