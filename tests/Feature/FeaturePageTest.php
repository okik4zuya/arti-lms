<?php

namespace Tests\Feature;

use Tests\TestCase;

class FeaturePageTest extends TestCase
{
    public function test_page_lists_all_ten_skills(): void
    {
        $response = $this->get('/fitur')->assertOk();

        foreach ([
            'ARTi-setup', 'ARTi-idea', 'ARTi-jfinder', 'ARTi-crosscite', 'ARTi-scratchbook',
            'ARTi-writing', 'ARTi-figure', 'ARTi-SLR', 'ARTi-ref', 'ARTi-annotate',
        ] as $skill) {
            $response->assertSee($skill);
        }
    }

    public function test_page_lists_the_real_tools_and_hides_unfinished_ones(): void
    {
        $response = $this->get('/fitur')->assertOk();

        foreach ([
            'arti-lit', 'arti-pdf-ingest', 'arti-ref-search-mcp', 'citation-chase', 'arti-jfinder',
            'arti-docx', 'arti-pdf', 'arti-annotate', 'arti-render', 'arti-compose', 'arti-plot',
            'arti-db', 'arti-memcheck',
        ] as $tool) {
            $response->assertSee($tool);
        }

        // Stubs, a brief with no code, an unused connector and internals are not sold as features.
        foreach (['arti-table', 'scopus-ris-batch-export', 'scite.ai', 'Smart Citations', 'arti-license', '_shared'] as $hidden) {
            $response->assertDontSee($hidden);
        }
    }

    public function test_page_states_the_limits_and_the_unfinished_ecourse(): void
    {
        $this->get('/fitur')
            ->assertSee('Tidak menjamin naskah diterima')
            ->assertSee('Tidak menjanjikan lolos detektor AI')
            ->assertSee('Statusnya belum lengkap')
            ->assertSee('tidak berafiliasi dengan Anthropic');
    }

    public function test_page_uses_the_analogy_animation_and_dashboard_and_the_files_exist(): void
    {
        $this->get('/fitur')
            ->assertSee('videos/landing/model-skill-alat.mp4', false)
            ->assertSee('images/landing/model-skill-alat.webp', false)
            ->assertSee('images/landing/dashboard.webp', false);

        $this->assertFileExists(public_path('videos/landing/model-skill-alat.mp4'));
        $this->assertFileExists(public_path('images/landing/model-skill-alat.webp'));
    }

    public function test_buy_button_points_to_whatsapp_when_a_number_is_configured(): void
    {
        config(['site.contact_whatsapp' => '081234567890']);

        $this->get('/fitur')->assertSee('https://wa.me/6281234567890?text=', false);
    }
}
