<?php

namespace App\Http\Controllers;

use App\Support\MarkdownRenderer;
use Illuminate\Support\Facades\Cache;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Yaml\Yaml;

/**
 * Chapter 1 of the e-course, readable without an account. Which subchapters are free is not
 * configured separately: it is the first chapter of the first section in the manifest.
 */
class FreeChapterController extends Controller
{
    protected const COURSE = 'arti-framework';

    public function start()
    {
        return redirect()->route('free.show', $this->subchapters()[0]['slug']);
    }

    public function show(MarkdownRenderer $markdown, string $slug)
    {
        $subchapters = $this->subchapters();
        $index = collect($subchapters)->search(fn (array $item) => $item['slug'] === $slug);

        abort_if($index === false, 404);

        $path = resource_path('content/'.self::COURSE."/{$slug}/index.md");

        abort_unless(is_file($path), 404);

        $document = YamlFrontMatter::parseFile($path);

        abort_unless($document->matter('published', false), 404);

        $html = $markdown->rewriteImages(
            $markdown->toHtml($this->resolveWikiLinks($document->body(), $subchapters)),
            fn (string $file) => route('free.image', ['slug' => $slug, 'file' => $file])
        );

        return view('site.baca', [
            'title' => $document->matter('title'),
            'html' => $html,
            'position' => $index + 1,
            'total' => count($subchapters),
            'previous' => $subchapters[$index - 1] ?? null,
            'next' => $subchapters[$index + 1] ?? null,
        ]);
    }

    public function image(string $slug, string $file): BinaryFileResponse
    {
        abort_unless(collect($this->subchapters())->contains(fn (array $item) => $item['slug'] === $slug), 404);

        $path = resource_path('content/'.self::COURSE."/{$slug}/images/{$file}");

        abort_unless(is_file($path) && ! str_contains($file, '..'), 404);

        return response()->file($path);
    }

    /** @return list<array{slug: string, title: string}> */
    public function subchapters(): array
    {
        $manifest = Cache::remember('content.manifest.'.self::COURSE, now()->addHour(), function () {
            $path = resource_path('content/'.self::COURSE.'/manifest.yaml');

            abort_unless(is_file($path), 404);

            return Yaml::parseFile($path);
        });

        return array_values($manifest['sections'][0]['chapters'][0]['subchapters'] ?? []);
    }

    /** A [[Title]] link becomes a real link when the target is also free, otherwise plain text. */
    protected function resolveWikiLinks(string $body, array $subchapters): string
    {
        return preg_replace_callback('/\[\[([^\]]+)\]\]/', function (array $matches) use ($subchapters) {
            $title = trim($matches[1]);
            $target = collect($subchapters)->first(fn (array $item) => strcasecmp($item['title'] ?? '', $title) === 0);

            return $target ? '['.$title.']('.route('free.show', $target['slug']).')' : $title;
        }, $body);
    }
}
