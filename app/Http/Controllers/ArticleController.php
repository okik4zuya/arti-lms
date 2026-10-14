<?php

namespace App\Http\Controllers;

use App\Support\ArticleRepository;
use App\Support\MarkdownRenderer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ArticleController extends Controller
{
    public function __construct(protected ArticleRepository $articles) {}

    public function index()
    {
        return view('articles.index', [
            'articles' => $this->articles->all(),
            'tags' => $this->articles->tags(),
        ]);
    }

    public function tag(string $tag)
    {
        $articles = $this->articles->withTag($tag);

        abort_if($articles->isEmpty(), 404);

        return view('articles.index', [
            'articles' => $articles,
            'tags' => $this->articles->tags(),
            'tag' => $tag,
        ]);
    }

    public function show(MarkdownRenderer $markdown, string $slug)
    {
        $article = $this->articles->find($slug);

        abort_unless($article, 404);

        $html = $markdown->rewriteImages(
            $markdown->toHtml($article->body),
            fn (string $file) => route('articles.image', ['slug' => $slug, 'file' => $file])
        );

        return view('articles.show', ['article' => $article, 'html' => $html]);
    }

    public function image(string $slug, string $file): BinaryFileResponse
    {
        $path = $this->articles->imagePath($slug, $file);

        abort_unless($path, 404);

        return response()->file($path);
    }

    public function feed()
    {
        return response()
            ->view('articles.feed', ['articles' => $this->articles->all()->take(20)])
            ->header('Content-Type', 'application/atom+xml; charset=UTF-8');
    }
}
