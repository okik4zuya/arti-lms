<?php

namespace Tests\Feature;

use Tests\TestCase;

class ArtiPdfPageTest extends TestCase
{
    public function test_page_has_the_download_cta_and_the_honest_limits(): void
    {
        $this->get('/artipdf')
            ->assertOk()
            ->assertSee(config('site.artipdf_url'), false)
            ->assertSee('ArtiPDFSetup-'.config('site.artipdf_version').'.exe')
            ->assertSee('Tabel dan persamaan tidak dipertahankan')
            ->assertSee('OCR hanya berbahasa Inggris')
            ->assertSee('belum bertanda tangan digital')
            ->assertSee('Hanya Windows');
    }

    public function test_page_is_free_and_does_not_mention_macos_or_a_compression_percentage(): void
    {
        $this->get('/artipdf')
            ->assertDontSee('Beli')
            ->assertDontSee('data-kontak', false)
            ->assertDontSee('80%')
            ->assertDontSee('Summarize with AI');
    }

    public function test_page_uses_real_media_and_every_file_exists(): void
    {
        $response = $this->get('/artipdf')->assertOk();

        foreach (['artipdf-editor', 'artipdf-merge', 'artipdf-convert'] as $name) {
            $response->assertSee("images/landing/$name.webp", false);
            $this->assertFileExists(public_path("images/landing/$name.webp"));
        }
    }

    public function test_page_is_in_the_sitemap_and_linked_from_the_free_section(): void
    {
        $this->get('/sitemap.xml')->assertSee(route('artipdf'), false);
        $this->get('/')->assertSee('href="'.route('artipdf').'"', false);
    }
}
