<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Free tools hub (/tools): pages, the SITE_TOOLS_ENABLED switch, Gem texts and the rules that keep the tools
 * client-only (no third-party origin in the page, every Vite entry registered).
 */
class ToolsPagesTest extends TestCase
{
    protected const SLUGS = ['composite', 'diagram', 'plot', 'profil-jurnal'];

    public function test_the_hub_and_every_tool_page_answer_with_one_h1(): void
    {
        foreach (array_merge(['/tools'], array_map(fn ($s) => "/tools/$s", self::SLUGS)) as $path) {
            $html = $this->get($path)->assertOk()->getContent();

            $this->assertSame(1, preg_match_all('/<h1[\s>]/', $html), "$path should have exactly one h1");
            $this->assertStringContainsString('<a href="#isi" class="skip-link">', $html, $path);
        }
    }

    public function test_titles_and_descriptions_are_unique(): void
    {
        $titles = $descriptions = [];

        foreach (array_merge(['/tools'], array_map(fn ($s) => "/tools/$s", self::SLUGS)) as $path) {
            $html = $this->get($path)->getContent();
            preg_match('#<title>(.*?)</title>#s', $html, $title);
            preg_match('#<meta name="description" content="(.*?)">#s', $html, $description);

            $titles[$title[1]][] = $path;
            $descriptions[$description[1]][] = $path;
        }

        foreach ([$titles, $descriptions] as $seen) {
            foreach ($seen as $paths) {
                $this->assertCount(1, $paths, 'Duplicate on: '.implode(', ', $paths));
            }
        }
    }

    public function test_an_unknown_tool_is_a_404(): void
    {
        $this->get('/tools/tidak-ada')->assertNotFound();
        $this->get('/tools/tidak-ada/pengetahuan.md')->assertNotFound();
    }

    public function test_switched_off_the_pages_are_noindex_and_hidden_from_menu_and_sitemap(): void
    {
        config(['site.tools_enabled' => false]);

        $this->get('/tools')->assertSee('<meta name="robots" content="noindex">', false);
        $this->get('/tools/diagram')->assertSee('<meta name="robots" content="noindex">', false);
        $this->get('/fitur')->assertDontSee(route('tools.index'), false);
        $this->get('/sitemap.xml')->assertDontSee(route('tools.index'), false);
    }

    public function test_switched_on_the_pages_are_indexable_and_in_menu_and_sitemap(): void
    {
        config(['site.tools_enabled' => true]);

        $this->get('/tools')->assertDontSee('<meta name="robots" content="noindex">', false);
        $this->get('/fitur')->assertSee('href="'.route('tools.index').'"', false);
        $sitemap = $this->get('/sitemap.xml');
        $sitemap->assertSee(route('tools.index'), false);
        foreach (self::SLUGS as $slug) {
            $sitemap->assertSee(route('tools.show', $slug), false);
        }
    }

    public function test_the_gem_button_shows_only_when_a_share_link_is_set(): void
    {
        config(['tools.tools.diagram.gem_url' => null]);
        $this->get('/tools/diagram')->assertSee('Salin instruksi')->assertDontSee('Buka Gem di Gemini');

        config(['tools.tools.diagram.gem_url' => 'https://gemini.google.com/gem/abc123']);
        $this->get('/tools/diagram')
            ->assertSee('href="https://gemini.google.com/gem/abc123"', false)
            ->assertSee('Buka Gem di Gemini');
    }

    public function test_every_tool_has_gem_texts_and_the_knowledge_file_downloads(): void
    {
        foreach (config('tools.tools') as $slug => $tool) {
            foreach (['instruction', 'knowledge'] as $key) {
                $path = resource_path('gems/'.$tool[$key]);
                $this->assertFileExists($path);
                $this->assertGreaterThan(200, strlen(trim(file_get_contents($path))), $tool[$key]);
            }

            // Gemini's instruction limit is unverified (one source says about 4,000 characters); stay under it.
            $this->assertLessThanOrEqual(4000, mb_strlen(file_get_contents(resource_path('gems/'.$tool['instruction']))), "$slug instruction too long");

            $this->get("/tools/$slug/pengetahuan.md")
                ->assertOk()
                ->assertHeader('Content-Disposition', 'attachment; filename="arti-'.$slug.'-pengetahuan.md"');
        }
    }

    public function test_the_instruction_is_shown_on_the_page(): void
    {
        $text = trim(file_get_contents(resource_path('gems/diagram.instruksi.md')));

        $this->get('/tools/diagram')->assertSee(substr($text, 0, 60), false);
    }

    public function test_pages_load_nothing_from_a_third_party_origin(): void
    {
        foreach (array_merge(['/tools'], array_map(fn ($s) => "/tools/$s", self::SLUGS)) as $path) {
            $html = $this->get($path)->getContent();

            preg_match_all('#<(?:script|link|img|iframe)\b[^>]*\s(?:src|href)="(https?://[^"]+)"#', $html, $m);
            foreach ($m[1] as $url) {
                $this->assertStringStartsWith(config('app.url'), $url, "$path loads $url");
            }
            $this->assertStringNotContainsString('fonts.googleapis.com', $html, $path);
        }
    }

    public function test_every_tool_entry_is_registered_in_vite(): void
    {
        $vite = file_get_contents(base_path('vite.config.js'));

        foreach (config('tools.tools') as $tool) {
            if ($tool['entry']) {
                $this->assertStringContainsString($tool['entry'], $vite, $tool['entry']);
                $this->assertFileExists(base_path($tool['entry']));
            }
        }
        $this->assertStringContainsString('resources/js/tools/gem.js', $vite);
    }

    public function test_the_hub_links_every_tool_and_the_sign_up_form_uses_a_known_source(): void
    {
        $response = $this->get('/tools');
        foreach (self::SLUGS as $slug) {
            $response->assertSee(route('tools.show', $slug), false);
        }
        $response->assertSee('name="source" value="tools"', false);

        $this->assertArrayHasKey('tools', \App\Models\Subscriber::SOURCES);
    }

    public function test_the_workbench_markup_is_present_on_the_three_tool_pages(): void
    {
        foreach (['composite', 'diagram', 'plot'] as $slug) {
            $this->get("/tools/$slug")->assertSee('data-workbench', false)->assertSee('data-wb-copy-errors', false);
        }
        $this->get('/tools/profil-jurnal')->assertDontSee('data-workbench', false);
    }
}
