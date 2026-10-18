<?php

namespace App\Support;

use App\Models\Course;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Symfony\Component\Yaml\Yaml;
use Throwable;

/**
 * Reads course content (manifest, subchapter pages) and keeps the parsed result in the cache.
 *
 * Every key carries the modification time of the file it came from, so editing a Markdown file or the manifest
 * shows at once and nothing has to be cleared by hand. The page key also carries the site address, because the
 * rendered HTML holds absolute image URLs.
 */
class CourseContent
{
    public function __construct(private readonly MarkdownRenderer $markdown) {}

    /** The course manifest. A missing file is a 404 unless $required is false (then an empty array). */
    public function manifest(string $courseSlug, bool $required = true): array
    {
        $path = resource_path("content/{$courseSlug}/manifest.yaml");

        if (! is_file($path)) {
            abort_if($required, 404);

            return [];
        }

        return $this->remember("course.manifest.{$courseSlug}.".filemtime($path), fn () => Yaml::parseFile($path));
    }

    /** Title and published flag of one subchapter, or null when its file does not exist. */
    public function meta(string $courseSlug, string $slug): ?array
    {
        $path = $this->pagePath($courseSlug, $slug);

        if (! is_file($path)) {
            return null;
        }

        return $this->remember("course.meta.{$courseSlug}.{$slug}.".filemtime($path), function () use ($path) {
            $matter = YamlFrontMatter::parseFile($path);

            return ['title' => $matter->matter('title'), 'published' => (bool) $matter->matter('published', false)];
        });
    }

    /** The rendered subchapter ['title' => ..., 'html' => ...], or null when it is missing or not published. */
    public function page(Course $course, string $slug): ?array
    {
        $meta = $this->meta($course->slug, $slug);

        if ($meta === null || ! $meta['published']) {
            return null;
        }

        $manifestPath = resource_path("content/{$course->slug}/manifest.yaml");
        $key = implode('.', [
            'course.page', $course->slug, $slug,
            filemtime($this->pagePath($course->slug, $slug)),
            is_file($manifestPath) ? filemtime($manifestPath) : 0,
            md5(url('/')),
        ]);

        return $this->remember($key, function () use ($course, $slug, $meta) {
            $document = YamlFrontMatter::parseFile($this->pagePath($course->slug, $slug));
            $body = $this->resolveWikiLinks($document->body(), $course, $this->manifest($course->slug));

            $html = $this->markdown->rewriteImages(
                $this->markdown->toHtml($body),
                fn (string $file) => route('content.image', ['course' => $course, 'slug' => $slug, 'file' => $file])
            );

            return ['title' => $meta['title'], 'html' => $html];
        });
    }

    /** The closest published subchapter before (-1) or after (+1) a position in a flat list, or null at either end. */
    public function neighbour(string $courseSlug, array $subchapters, int $position, int $step): ?array
    {
        for ($i = $position + $step; isset($subchapters[$i]); $i += $step) {
            if ($this->meta($courseSlug, $subchapters[$i]['slug'])['published'] ?? false) {
                return ['slug' => $subchapters[$i]['slug'], 'title' => $subchapters[$i]['title'] ?? $subchapters[$i]['slug']];
            }
        }

        return null;
    }

    private function pagePath(string $courseSlug, string $slug): string
    {
        return resource_path("content/{$courseSlug}/{$slug}/index.md");
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

    /** Cache read-through that survives a broken cache: on any cache error the value is computed and a warning logged. */
    private function remember(string $key, Closure $compute): array
    {
        try {
            $store = Cache::store(config('content.cache_store'));
            $hit = $store->get($key);

            if (is_array($hit)) {
                return $hit;
            }
        } catch (Throwable $e) {
            Log::warning('Course content cache unavailable, rendering without it: '.$e->getMessage());

            return $compute();
        }

        $value = $compute();

        try {
            $store->put($key, $value, now()->addDays(max(1, (int) config('content.cache_days'))));
        } catch (Throwable $e) {
            Log::warning('Course content cache write failed: '.$e->getMessage());
        }

        return $value;
    }
}
