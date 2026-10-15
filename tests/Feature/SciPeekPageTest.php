<?php

namespace Tests\Feature;

use Tests\TestCase;

class SciPeekPageTest extends TestCase
{
    public function test_page_has_the_install_cta_and_the_honest_limits(): void
    {
        $this->get('/scipeek')
            ->assertOk()
            ->assertSee(config('site.scipeek_url'), false)
            ->assertSee('Bukan pendeteksi jurnal predator')
            ->assertSee('bukan Impact Factor')
            ->assertSee('tidak berafiliasi dengan Scimago');
    }

    public function test_page_is_free_and_non_commercial(): void
    {
        // The Scimago data is free only for non-commercial use, so no price or buy button here.
        $this->get('/scipeek')
            ->assertDontSee('Beli')
            ->assertDontSee('Rp99')
            ->assertDontSee('data-kontak', false);
    }

    public function test_page_uses_real_media_and_every_file_exists(): void
    {
        $response = $this->get('/scipeek')->assertOk();

        foreach ([
            'images/landing/scipeek-scopus-asli.webp', 'images/landing/scipeek-sciencedirect.webp',
            'images/landing/scipeek-scholar.webp', 'images/landing/scipeek-tab.webp',
            'images/landing/scipeek-poster.webp', 'videos/landing/scipeek-tutorial.mp4',
        ] as $file) {
            $response->assertSee($file, false);
            $this->assertFileExists(public_path($file));
        }
    }

    public function test_page_is_in_the_sitemap_and_linked_from_the_free_section(): void
    {
        $this->get('/sitemap.xml')->assertSee(route('scipeek'), false);
        $this->get('/')->assertSee('href="'.route('scipeek').'"', false);
    }
}
