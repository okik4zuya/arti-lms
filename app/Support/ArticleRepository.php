<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\YamlFrontMatter\YamlFrontMatter;

/**
 * Flat-file articles: <articles path>/<slug>/index.md with front matter
 * (title, description, date, tags, published) and an optional images/ folder.
 */
class ArticleRepository
{
    /** Slugs that would collide with a sibling route under /artikel. */
    public const RESERVED_SLUGS = ['tag'];

    public function path(): string
    {
        return config('articles.path');
    }

    /**
     * Published articles, newest first.
     *
     * @return Collection<int, Article>
     */
    public function all(): Collection
    {
        return Cache::remember('articles.index.'.$this->fingerprint(), now()->addDay(), function () {
            return collect(glob($this->path().'/*/index.md') ?: [])
                ->map(fn (string $file) => $this->parse($file))
                ->filter()
                ->sortByDesc(fn (Article $article) => $article->date->getTimestamp())
                ->values();
        });
    }

    public function find(string $slug): ?Article
    {
        return $this->all()->firstWhere('slug', $slug);
    }

    /**
     * @return Collection<int, Article>
     */
    public function withTag(string $tag): Collection
    {
        return $this->all()->filter(fn (Article $article) => in_array($tag, $article->tags, true))->values();
    }

    /**
     * Tag => number of articles, most used first.
     *
     * @return Collection<string, int>
     */
    public function tags(): Collection
    {
        return $this->all()
            ->flatMap(fn (Article $article) => $article->tags)
            ->countBy()
            ->sortDesc();
    }

    public function imagePath(string $slug, string $file): ?string
    {
        if (str_contains($file, '..') || str_contains($slug, '..') || ! $this->find($slug)) {
            return null;
        }

        $path = $this->path().'/'.$slug.'/images/'.$file;

        return is_file($path) ? $path : null;
    }

    /**
     * Changes whenever a file is added, removed or edited, so the cached index never goes stale.
     */
    protected function fingerprint(): string
    {
        $parts = collect(glob($this->path().'/*/index.md') ?: [])
            ->map(fn (string $file) => $file.':'.filemtime($file));

        return md5($this->path().'|'.$parts->implode('|'));
    }

    protected function parse(string $file): ?Article
    {
        $slug = basename(dirname($file));

        if (in_array($slug, self::RESERVED_SLUGS, true) || ! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
            return null;
        }

        $document = YamlFrontMatter::parseFile($file);

        if (! $document->matter('published', false) || ! $document->matter('title') || ! $document->matter('date')) {
            return null;
        }

        $date = $this->toDate($document->matter('date'));

        if ($date->isFuture()) {
            return null;
        }

        return new Article(
            slug: $slug,
            title: (string) $document->matter('title'),
            description: (string) $document->matter('description', Str::limit(strip_tags($document->body()), 160)),
            date: $date,
            tags: collect($document->matter('tags', []))->map(fn ($tag) => Str::slug((string) $tag))->filter()->unique()->values()->all(),
            body: $document->body(),
        );
    }

    /**
     * Unquoted YAML dates arrive as Unix timestamps, quoted ones as strings.
     */
    protected function toDate(int|string $value): Carbon
    {
        return is_int($value)
            ? Carbon::createFromTimestampUTC($value)
            : Carbon::parse($value, 'UTC');
    }
}
