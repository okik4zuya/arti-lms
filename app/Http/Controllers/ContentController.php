<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ContentController extends Controller
{
    public function show(Request $request, Course $course, string $slug)
    {
        $path = resource_path("content/{$course->slug}/{$slug}/index.md");

        abort_unless(is_file($path), 404);

        $document = YamlFrontMatter::parseFile($path);

        abort_unless($document->matter('published', false), 404);

        $manifest = $this->manifest($course->slug);

        $body = $this->resolveWikiLinks($document->body(), $course, $manifest);

        $converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'allow',
            'disallowed_raw_html' => [
                'disallowed_tags' => ['title', 'textarea', 'xmp', 'iframe', 'noembed', 'noframes', 'script', 'plaintext'],
            ],
        ]);

        $html = $converter->convert($body)->getContent();
        $html = $this->resolveImageUrls($html, $course, $slug);

        $completedSlugs = $request->user()
            ->progress()
            ->where('course_id', $course->id)
            ->whereNotNull('completed_at')
            ->pluck('subchapter_slug');

        return view('content.show', [
            'course' => $course,
            'slug' => $slug,
            'title' => $document->matter('title'),
            'html' => $html,
            'manifest' => $manifest,
            'completedSlugs' => $completedSlugs,
            'isCompleted' => $completedSlugs->contains($slug),
        ]);
    }

    public function image(Course $course, string $slug, string $file): BinaryFileResponse
    {
        $path = resource_path("content/{$course->slug}/{$slug}/images/{$file}");

        abort_unless(is_file($path) && ! str_contains($file, '..'), 404);

        return response()->file($path);
    }

    protected function resolveWikiLinks(string $body, Course $course, array $manifest): string
    {
        $subchapters = collect($manifest['sections'] ?? [])
            ->flatMap(fn (array $section) => $section['chapters'] ?? [])
            ->flatMap(fn (array $chapter) => $chapter['subchapters'] ?? []);

        return preg_replace_callback(
            '/\[\[([^\]]+)\]\]/',
            function (array $matches) use ($course, $subchapters) {
                $title = trim($matches[1]);

                $target = $subchapters->first(
                    fn (array $subchapter) => strcasecmp($subchapter['title'] ?? '', $title) === 0
                );

                if (! $target) {
                    return $title;
                }

                $url = route('content.show', ['course' => $course, 'slug' => $target['slug']]);

                return "[{$title}]({$url})";
            },
            $body
        );
    }

    protected function resolveImageUrls(string $html, Course $course, string $slug): string
    {
        return preg_replace_callback(
            '/<img\s+([^>]*?)src="(?!https?:\/\/|\/)(?:\.\/)?images\/([^"]+)"/i',
            fn (array $matches) => sprintf(
                '<img %ssrc="%s"',
                $matches[1],
                route('content.image', ['course' => $course, 'slug' => $slug, 'file' => $matches[2]])
            ),
            $html
        );
    }

    protected function manifest(string $courseSlug): array
    {
        return Cache::remember("content.manifest.{$courseSlug}", now()->addHour(), function () use ($courseSlug) {
            $path = resource_path("content/{$courseSlug}/manifest.yaml");

            abort_unless(is_file($path), 404);

            return \Symfony\Component\Yaml\Yaml::parseFile($path);
        });
    }
}
