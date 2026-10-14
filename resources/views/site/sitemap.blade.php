{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($pages as $page)
    <url><loc>{{ $page }}</loc></url>
@endforeach
@foreach ($articles as $article)
    <url><loc>{{ route('articles.show', $article->slug) }}</loc><lastmod>{{ $article->date->toDateString() }}</lastmod></url>
@endforeach
@foreach ($tags as $tag => $count)
    <url><loc>{{ route('articles.tag', $tag) }}</loc></url>
@endforeach
</urlset>
