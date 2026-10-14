<?php

namespace Tests\Feature;

use App\Support\ArticleRepository;
use Tests\TestCase;

class ArticlesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['articles.path' => base_path('tests/Fixtures/articles')]);
    }

    public function test_index_lists_published_articles_newest_first(): void
    {
        $this->get('/artikel')
            ->assertOk()
            ->assertSeeInOrder(['Memori antar sesi', 'Catatan lama'])
            ->assertDontSee('Draf rahasia')
            ->assertDontSee('Terbit nanti')
            ->assertDontSee('Slug yang dicadangkan');
    }

    public function test_show_renders_markdown_tables_and_rewrites_relative_images(): void
    {
        $this->get('/artikel/memori-antar-sesi')
            ->assertOk()
            ->assertSee('<title>Memori antar sesi — ARTi Framework</title>', false)
            ->assertSee('<table>', false)
            ->assertSee('1 Oktober 2026')
            ->assertSee(route('articles.image', ['slug' => 'memori-antar-sesi', 'file' => 'dot.png']), false)
            ->assertSee('property="og:type" content="article"', false);
    }

    public function test_quoted_and_unquoted_front_matter_dates_both_work(): void
    {
        $articles = app(ArticleRepository::class)->all();

        $this->assertSame('2026-10-01', $articles->firstWhere('slug', 'memori-antar-sesi')->date->toDateString());
        $this->assertSame('2026-09-01', $articles->firstWhere('slug', 'catatan-lama')->date->toDateString());
    }

    public function test_unpublished_future_and_reserved_slug_articles_are_404(): void
    {
        $this->get('/artikel/belum-terbit')->assertNotFound();
        $this->get('/artikel/masa-depan')->assertNotFound();
        $this->get('/artikel/tag')->assertNotFound();
        $this->get('/artikel/tidak-ada')->assertNotFound();
    }

    public function test_tag_page_filters_articles(): void
    {
        $this->get('/artikel/tag/memori')
            ->assertOk()
            ->assertSee('Memori antar sesi')
            ->assertDontSee('Catatan lama');

        $this->get('/artikel/tag/riset')
            ->assertOk()
            ->assertSee('Memori antar sesi')
            ->assertSee('Catatan lama');

        $this->get('/artikel/tag/tidak-ada')->assertNotFound();
    }

    public function test_article_image_is_served_and_guarded(): void
    {
        $this->get('/artikel/memori-antar-sesi/images/dot.png')->assertOk();
        $this->get('/artikel/memori-antar-sesi/images/nope.png')->assertNotFound();
        $this->get('/artikel/belum-terbit/images/dot.png')->assertNotFound();
        $this->get('/artikel/memori-antar-sesi/images/..%2Findex.md')->assertNotFound();
    }

    public function test_feed_is_valid_atom_with_published_articles_only(): void
    {
        $response = $this->get('/feed.xml')->assertOk();

        $this->assertStringStartsWith('application/atom+xml', $response->headers->get('Content-Type'));

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml);
        $this->assertSame('http://www.w3.org/2005/Atom', $xml->getNamespaces()[''] ?? null);

        $titles = collect($xml->xpath('//*[local-name()="entry"]/*[local-name()="title"]'))
            ->map(fn ($title) => (string) $title)
            ->all();
        $this->assertSame(['Memori antar sesi', 'Catatan lama'], $titles);
    }

    public function test_empty_article_directory_renders_an_empty_state_and_valid_feed(): void
    {
        config(['articles.path' => base_path('tests/Fixtures/no-articles')]);

        $this->get('/artikel')->assertOk()->assertSee('Belum ada artikel.');
        $this->get('/')->assertOk()->assertSee('Belum ada artikel.');

        $xml = simplexml_load_string($this->get('/feed.xml')->getContent());
        $this->assertNotFalse($xml);
        $this->assertCount(0, $xml->xpath('//*[local-name()="entry"]'));
    }
}
