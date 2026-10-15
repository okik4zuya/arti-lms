<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Cross-page checks for the public site: one h1, unique title and description, canonical, lang, the
 * skip link, Indonesian error pages, and the hard copy rules (no macOS, nothing leaves for Google Fonts).
 */
class PublicPageQualityTest extends TestCase
{
    protected const PAGES = [
        '/', '/fitur', '/scipeek', '/artipdf', '/kontak', '/privasi', '/syarat', '/artikel',
        '/baca/01-2-masalah-utama', '/baca/02-solusi-parsial',
        '/baca/03-halusinasi-meruntuhkan-kredibilitas-akademik', '/baca/04-hilang-memori-tak-berarti',
    ];

    public function test_every_public_page_has_one_h1_a_canonical_a_lang_and_the_skip_link(): void
    {
        foreach (self::PAGES as $path) {
            $html = $this->get($path)->assertOk()->getContent();

            $this->assertSame(1, preg_match_all('/<h1[\s>]/', $html), "$path should have exactly one h1");
            $this->assertStringContainsString('<html lang="id">', $html, $path);
            $this->assertMatchesRegularExpression('/<link rel="canonical" href="[^"]+">/', $html, $path);
            $this->assertStringContainsString('<a href="#isi" class="skip-link">', $html, $path);
            $this->assertStringContainsString('<main id="isi"', $html, $path);
        }
    }

    public function test_titles_and_descriptions_are_unique_across_public_pages(): void
    {
        $titles = $descriptions = [];

        foreach (self::PAGES as $path) {
            $html = $this->get($path)->getContent();
            preg_match('#<title>(.*?)</title>#s', $html, $title);
            preg_match('#<meta name="description" content="(.*?)">#s', $html, $description);

            $this->assertNotEmpty($title[1] ?? '', "$path has no title");
            $this->assertNotEmpty($description[1] ?? '', "$path has no description");

            $titles[$title[1]][] = $path;
            $descriptions[$description[1]][] = $path;
        }

        foreach ([$titles, $descriptions] as $seen) {
            foreach ($seen as $text => $paths) {
                $this->assertCount(1, $paths, 'Duplicate on: '.implode(', ', $paths));
            }
        }
    }

    public function test_the_empty_article_index_is_noindex_and_out_of_the_sitemap(): void
    {
        config(['articles.path' => base_path('tests/Fixtures/no-articles')]);

        $this->get('/artikel')->assertOk()->assertSee('<meta name="robots" content="noindex">', false);
        $this->get('/sitemap.xml')->assertDontSee(route('articles.index'), false);
    }

    public function test_the_article_index_is_indexable_and_listed_once_there_are_articles(): void
    {
        config(['articles.path' => base_path('tests/Fixtures/articles')]);

        $this->get('/artikel')->assertDontSee('<meta name="robots" content="noindex">', false);
        $this->get('/sitemap.xml')->assertSee(route('articles.index'), false);
    }

    public function test_unknown_pages_get_an_indonesian_404_that_is_not_indexed(): void
    {
        $this->get('/halaman-yang-tidak-ada')
            ->assertNotFound()
            ->assertSee('Halaman tidak ditemukan')
            ->assertSee('Ke beranda')
            ->assertSee('<html lang="id">', false)
            ->assertSee('<meta name="robots" content="noindex">', false)
            ->assertDontSee('Not Found');
    }

    public function test_the_other_error_pages_are_indonesian_too(): void
    {
        foreach ([403 => 'Anda tidak punya akses', 419 => 'Sesi sudah berakhir', 429 => 'Terlalu banyak permintaan', 500 => 'Ada masalah di server ARTi', 503 => 'Sedang dalam pemeliharaan'] as $code => $text) {
            $html = view()->exists("errors.$code") ? view("errors.$code")->render() : view('errors.5xx')->render();

            $this->assertStringContainsString($text, $html, "error $code");
        }
    }

    public function test_login_is_indonesian_and_noindex(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('<html lang="id">', false)
            ->assertSee('<meta name="robots" content="noindex">', false)
            ->assertSee('Kata sandi')
            ->assertDontSee('Remember me');
    }

    public function test_no_public_page_mentions_macos(): void
    {
        foreach (self::PAGES as $path) {
            $this->assertDoesNotMatchRegularExpression('/\bmac(os)?\b/i', strip_tags($this->get($path)->getContent()), $path);
        }
    }

    public function test_no_public_page_or_chapter_loads_a_third_party_font(): void
    {
        foreach (self::PAGES as $path) {
            $html = $this->get($path)->getContent();

            $this->assertStringNotContainsString('fonts.googleapis.com', $html, $path);
            $this->assertStringNotContainsString('fonts.bunny.net', $html, $path);
        }

        $this->assertFileExists(public_path('fonts/atkinson/atkinson-hyperlegible-latin-400-normal.woff2'));
        $this->assertFileExists(public_path('fonts/atkinson/OFL.txt'));
    }

    public function test_the_legal_pages_state_the_facts_the_landing_promises(): void
    {
        $this->get('/syarat')
            ->assertSee('Rp199.000')
            ->assertSee('Rp99.000')
            ->assertSee('7 hari sejak tanggal pembelian')
            ->assertSee('tidak berafiliasi dengan Anthropic');

        $this->get('/privasi')
            ->assertSee('Anthropic')
            ->assertSee('XSRF-TOKEN')
            ->assertSee('Font dihosting di situs ini sendiri');
    }
}
