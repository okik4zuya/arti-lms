<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingPageTest extends TestCase
{
    public function test_landing_shows_the_offer_and_honest_requirements(): void
    {
        config(['site.offer.launch_sold' => 37]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Rp99.000')
            ->assertSee('Tersisa')
            ->assertSee('63 dari 100')
            ->assertSee('Garansi 7 hari')
            ->assertSee('Windows saja')
            ->assertSee('tidak berafiliasi dengan Anthropic')
            ->assertSee('Dari ide sampai publish, bersama ARTi.');
    }

    public function test_landing_hides_the_counter_when_it_is_not_set(): void
    {
        config(['site.offer.launch_sold' => null]);

        $this->get('/')->assertDontSee('Tersisa')->assertSee('100 pembeli pertama');
    }

    public function test_landing_falls_back_to_normal_price_when_quota_is_used_up(): void
    {
        config(['site.offer.launch_sold' => 100]);

        $this->get('/')
            ->assertSee('Satu paket, satu harga.')
            ->assertDontSee('Harga perintis untuk');
    }

    public function test_buy_buttons_point_to_whatsapp_when_a_number_is_configured(): void
    {
        config(['site.contact_whatsapp' => '081234567890']);

        $this->get('/')->assertSee('https://wa.me/081234567890?text=', false);
    }

    public function test_demo_shows_a_soon_badge_until_a_video_is_set_and_the_chapter_link_can_be_overridden(): void
    {
        config(['site.free_chapter_url' => null, 'site.demo_video_embed_url' => null]);

        $this->get('/')->assertSee('Segera')->assertSee(route('free.start'), false);

        config(['site.free_chapter_url' => 'https://example.test/bab-1', 'site.demo_video_embed_url' => 'https://example.test/embed']);

        $this->get('/')
            ->assertSee('https://example.test/bab-1', false)
            ->assertSee('https://example.test/embed', false)
            ->assertDontSee('Video demo 1-2 menit');
    }

    public function test_demo_section_has_the_lazy_explainer_video_with_a_poster(): void
    {
        $this->get('/')
            ->assertSee('videos/landing/arti-explainer.mp4', false)
            ->assertSee('preload="none"', false)
            ->assertSee('images/landing/explainer-poster.webp', false);

        $this->assertFileExists(public_path('videos/landing/arti-explainer.mp4'));
        $this->assertFileExists(public_path('images/landing/explainer-poster.webp'));
    }

    public function test_package_section_shows_the_dashboard_screenshot(): void
    {
        $this->get('/')->assertSee('images/landing/dashboard.webp', false);

        $this->assertFileExists(public_path('images/landing/dashboard.webp'));
    }

    public function test_free_section_links_to_the_youtube_channel(): void
    {
        $this->get('/')
            ->assertSee('https://www.youtube.com/@dosenberarti', false)
            ->assertSee('Tonton di YouTube');
    }

    public function test_header_has_a_mobile_menu_with_a_short_buy_label(): void
    {
        $this->get('/')
            ->assertSee('class="nav-menu"', false)
            ->assertSee('cta-short', false)
            ->assertSee('nav-menu__account', false);
    }

    public function test_pages_carry_a_social_preview_image_that_exists(): void
    {
        $this->get('/')
            ->assertSee('property="og:image"', false)
            ->assertSee('images/landing/og-image.png', false)
            ->assertSee('summary_large_image', false);

        $this->assertFileExists(public_path('images/landing/og-image.png'));
        $this->assertFileExists(public_path('images/landing/diagram-tangga.png'));
    }

    public function test_landing_explains_the_error_chain_and_labels_it_an_illustration(): void
    {
        $this->get('/')
            ->assertSee('Dianggap benar')
            ->assertSee('Ilustrasi alur penyebaran kesalahan, bukan data.');
    }

    public function test_landing_uses_the_existing_media_and_every_file_exists(): void
    {
        $response = $this->get('/');

        foreach (['rithah.webp', 'scipeek-scopus.webp', 'youtube-channel.webp', 'ecourse.webp'] as $file) {
            $response->assertSee('images/landing/'.$file, false);
            $this->assertFileExists(public_path('images/landing/'.$file));
        }
    }

    public function test_landing_makes_no_first_claim_and_no_fabricated_proof(): void
    {
        $this->get('/')->assertDontSee('pertama di')->assertDontSee('testimoni');
    }
}
