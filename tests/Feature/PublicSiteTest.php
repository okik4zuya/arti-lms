<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['articles.path' => base_path('tests/Fixtures/articles')]);
    }

    public function test_landing_replaces_the_login_redirect(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('ARTi Framework')
            ->assertSee('Masuk')
            ->assertSee('tidak lupa dan tidak mengarang')
            ->assertSee('Sesi berikutnya melanjutkan, bukan mengulang');
    }

    #[DataProvider('staticPages')]
    public function test_static_pages_render(string $path, string $heading): void
    {
        $this->get($path)
            ->assertOk()
            ->assertSee($heading)
            ->assertSee('<link rel="canonical"', false);
    }

    public static function staticPages(): array
    {
        return [
            'fitur' => ['/fitur', 'Apa yang ada di dalam ARTi.'],
            'kontak' => ['/kontak', 'Hubungi ARTi.'],
            'privasi' => ['/privasi', 'Kebijakan privasi'],
            'syarat' => ['/syarat', 'Syarat penggunaan'],
        ];
    }

    public function test_contact_page_shows_configured_channels_only(): void
    {
        $this->get('/kontak')->assertSee('hi@arti.ai.id')->assertDontSee('belum dibuka');

        config(['site.contact_email' => 'halo@example.test']);

        $this->get('/kontak')->assertSee('halo@example.test')->assertDontSee('belum dibuka');

        config(['site.contact_email' => null]);

        $this->get('/kontak')->assertSee('belum dibuka');
    }

    public function test_old_pricing_and_download_urls_redirect_permanently(): void
    {
        $this->get('/harga')->assertStatus(301)->assertRedirect('/#harga');
        $this->get('/unduh')->assertStatus(301)->assertRedirect('/');
    }

    public function test_top_menu_has_beranda_fitur_harga_and_the_two_free_tools(): void
    {
        $html = $this->get('/fitur')->assertOk()->getContent();

        $this->assertStringContainsString(url('/').'#harga', $html);
        // The free chapter is reached from the landing page, not from the top menu.
        preg_match('#<nav class="site-nav site-nav--desktop".*?</nav>#s', $html, $nav);
        $this->assertStringNotContainsString('Baca bab 1 gratis', $nav[0]);
        $this->assertStringNotContainsString(route('free.start'), $nav[0]);
        $this->assertStringContainsString('>Beranda</a>', $html);
        $this->assertStringContainsString('ARTi PDF<span class="nav-badge">Free</span></a>', $html);
        $this->assertStringContainsString('ARTi SciPeek<span class="nav-badge">Free</span></a>', $html);
        // Kontak left the top menu (it stays in the footer and the floating button).
        $this->assertStringNotContainsString('<a href="'.route('kontak').'" >Kontak</a>', $html);
        $this->assertSame(1, substr_count($html, '>Kontak</a>'));
        $this->assertStringNotContainsString('>Unduh<', $html);
        $this->assertStringNotContainsString('>Artikel<', $html);
        $this->assertStringNotContainsString(route('articles.index').'"', $html);
    }

    public function test_robots_txt_is_served_by_the_app_and_points_to_the_sitemap(): void
    {
        $response = $this->get('/robots.txt')->assertOk();

        $this->assertStringStartsWith('text/plain', $response->headers->get('Content-Type'));
        $response->assertSee('Disallow: /jamrud')
            ->assertSee('Sitemap: '.route('sitemap'));
        $this->assertFileDoesNotExist(public_path('robots.txt'));
    }

    public function test_sitemap_lists_pages_published_articles_and_tags(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk();

        $this->assertStringStartsWith('application/xml', $response->headers->get('Content-Type'));
        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml);

        $locs = collect($xml->xpath('//*[local-name()="loc"]'))->map(fn ($loc) => (string) $loc);

        $this->assertTrue($locs->contains(route('home')), 'sitemap misses the landing page');

        foreach (['/fitur', '/artikel', '/kontak', '/privasi', '/syarat'] as $path) {
            $this->assertTrue($locs->contains(url($path)), "sitemap misses {$path}");
        }

        $this->assertTrue($locs->contains(url('/artikel/memori-antar-sesi')));
        $this->assertTrue($locs->contains(url('/artikel/tag/memori')));
        $this->assertFalse($locs->contains(url('/artikel/belum-terbit')));
        $this->assertFalse($locs->contains(url('/artikel/masa-depan')));
    }

    public function test_legacy_dashboard_url_redirects_permanently_to_app(): void
    {
        $this->get('/dashboard')->assertStatus(301)->assertRedirect('/app');
    }

    public function test_app_requires_authentication(): void
    {
        $this->get('/app')->assertRedirect('/login');
    }
}
