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
            ->assertSee('Sekali pembayaran')
            ->assertDontSee('Garansi')
            ->assertDontSee('uang kembali')
            ->assertSee('Windows saja')
            ->assertSee('tidak berafiliasi dengan Anthropic')
            ->assertSee('Dari ide sampai publish, bersama ARTi.');
    }

    public function test_landing_counts_places_held_by_orders_even_without_a_manual_count(): void
    {
        config(['site.offer.launch_sold' => null]);

        $this->get('/')->assertSee('Tersisa')->assertSee('100 dari 100')->assertSee('100 pembeli pertama');
    }

    public function test_landing_falls_back_to_normal_price_when_quota_is_used_up(): void
    {
        config(['site.offer.launch_sold' => 100]);

        $this->get('/')
            ->assertSee('Satu paket, satu harga.')
            ->assertDontSee('Harga perintis untuk');
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

    public function test_solution_section_shows_the_model_skill_tool_illustration_with_html_labels(): void
    {
        $this->get('/')
            ->assertSee('images/landing/model-skill-alat.webp', false)
            ->assertSee('Pengetahuan dasar')
            ->assertSee('Lembar cara kerja');

        $this->assertFileExists(public_path('images/landing/model-skill-alat.webp'));
    }

    public function test_examples_section_compares_without_and_with_memory(): void
    {
        $this->get('/')
            ->assertSee('images/landing/memori-duo.webp', false)
            ->assertSee('Tanpa memori')
            ->assertSee('Dengan memori');

        $this->assertFileExists(public_path('images/landing/memori-duo.webp'));
    }

    public function test_free_section_offers_the_artipdf_windows_app_with_honest_caveats(): void
    {
        $this->get('/')
            ->assertSee('images/landing/artipdf-editor.webp', false)
            ->assertSee('href="'.route('artipdf').'"', false)
            ->assertSee('Lihat ARTi PDF')
            ->assertSee('tabel dan persamaan tidak dipertahankan')
            ->assertSee('belum bertanda tangan')
            ->assertDontSee('macOS');

        $this->assertFileExists(public_path('images/landing/artipdf-editor.webp'));
    }

    public function test_free_section_links_to_the_youtube_channel(): void
    {
        $this->get('/')
            ->assertSee('https://www.youtube.com/@dosenberarti', false)
            ->assertSee('Tonton di YouTube');
    }

    public function test_header_has_a_mobile_menu_and_no_buy_button(): void
    {
        $html = $this->get('/')
            ->assertSee('class="nav-menu"', false)
            ->assertSee('nav-menu__account', false)
            ->assertDontSee('header-cta', false)
            ->getContent();

        // The first "Beli sekarang" on the page is the hero button, not a header one.
        $header = substr($html, strpos($html, '<header'), strpos($html, '</header>') - strpos($html, '<header'));
        $this->assertStringNotContainsString('Beli sekarang', $header);
    }

    public function test_the_hero_offers_a_free_try_that_jumps_to_the_free_section(): void
    {
        $this->get('/')
            ->assertSee('href="#coba-gratis"', false)
            ->assertSee('Coba gratis')
            ->assertSee('id="coba-gratis"', false);
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

        foreach (['benang-terurai.webp', 'scipeek-scopus.webp', 'youtube-channel.webp', 'ecourse.webp'] as $file) {
            $response->assertSee('images/landing/'.$file, false);
            $this->assertFileExists(public_path('images/landing/'.$file));
        }
    }

    public function test_landing_highlights_word_with_zotero_fields_without_a_rival_claim(): void
    {
        $this->get('/')
            ->assertSee('ARTi mengeluarkan berkas Word yang sudah berisi field Zotero.')
            ->assertSee('Butuh Zotero di Word.')
            ->assertDontSee('Tidak ada chat AI');
    }

    public function test_landing_states_the_figure_pain_and_its_limit(): void
    {
        $this->get('/')
            ->assertSee('<strong>gambarnya terlalu AI</strong>, jadi ragu memasukkan ke naskah.', false)
            ->assertSee('Gambar dari data, bukan dari imajinasi AI')
            ->assertSee('Bukan untuk foto atau ilustrasi bebas.');
    }

    public function test_the_hero_names_the_four_problems_and_goes_to_publish(): void
    {
        $this->get('/')
            ->assertSee('dari ide sampai publish.')
            ->assertSee('Tanpa referensi yang tidak bisa dilacak.')
            ->assertSee('Tanpa copy-paste ke Word.')
            ->assertDontSee('dari ide sampai submit');
    }

    public function test_the_four_problems_are_agitated_in_order_and_answered_in_the_same_order(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $tags = ['Soal kredibilitas', 'Soal tenaga', 'Soal gambar', 'Soal output'];
        $positions = [];
        foreach ($tags as $tag) {
            $this->assertSame(2, substr_count($html, $tag), $tag.' appears once as agitation and once as answer');
            $first = strpos($html, $tag);
            $positions[] = [$first, strpos($html, $tag, $first + 1)];
        }

        $this->assertSame($positions, collect($positions)->sortBy(0)->values()->all(), 'agitation order');
        $this->assertSame(array_column($positions, 1), collect(array_column($positions, 1))->sort()->values()->all(), 'answer order');
        $this->assertGreaterThan(max(array_column($positions, 0)), min(array_column($positions, 1)), 'answers come after all four problems');
        $this->assertStringNotContainsString('Kesalahan kecil yang tidak berhenti', $html);
    }

    public function test_every_answer_states_before_after_and_what_comes_back(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertSame(4, substr_count($html, 'answer__k answer__k--with'));
        $this->assertSame(4, substr_count($html, 'Yang kembali ke Anda:'));
        $this->assertStringContainsString('waktu dan tenaga', $html);
        $this->assertStringContainsString('kualitas', $html);
    }

    public function test_the_value_summary_and_the_claude_subscription_come_before_the_price(): void
    {
        $html = $this->get('/')
            ->assertSee('Yang kembali ke Anda.')
            ->assertSee('Bayar sekali')
            ->assertSee('Pengalaman saya:')
            ->assertSee('Soal langganan Claude.')
            ->assertSee('Rp13.000 per hari')
            ->assertSee('tidak harus berlangganan terus')
            ->assertSee('Ini bukan janji hasil')
            ->getContent();

        $this->assertLessThan(strpos($html, 'id="harga"'), strpos($html, 'id="langganan"'));
        $this->assertLessThan(strpos($html, 'id="langganan"'), strpos($html, 'id="manfaat"'));
    }

    public function test_the_closing_repeats_the_four_gains_without_absolute_promises(): void
    {
        $this->get('/')
            ->assertSee('Referensi yang bisa dilacak. Sesi yang melanjutkan. Gambar dari data. Sitasi yang bisa disegarkan.')
            ->assertDontSee('Tidak ada lagi')
            ->assertDontSee('dijamin');
    }

    public function test_landing_makes_no_first_claim_and_no_fabricated_proof(): void
    {
        $this->get('/')->assertDontSee('pertama di')->assertDontSee('testimoni');
    }
}
