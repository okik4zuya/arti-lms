<?php

namespace App\Http\Controllers;

use App\Support\ArticleRepository;

class SeoController extends Controller
{
    /** Public pages listed in the sitemap, by route name. */
    protected const PAGES = ['home', 'fitur', 'harga', 'unduh', 'articles.index', 'kontak', 'privasi', 'syarat'];

    public function sitemap(ArticleRepository $articles, FreeChapterController $free)
    {
        return response()
            ->view('site.sitemap', [
                'pages' => array_merge(
                    array_map(fn (string $name) => route($name), self::PAGES),
                    array_map(fn (array $item) => route('free.show', $item['slug']), $free->subchapters())
                ),
                'articles' => $articles->all(),
                'tags' => $articles->tags(),
            ])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots()
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /jamrud',
            'Disallow: /app',
            'Disallow: /learn',
            'Disallow: /login',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
