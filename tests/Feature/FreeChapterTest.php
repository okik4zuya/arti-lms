<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FreeChapterTest extends TestCase
{
    public function test_start_redirects_to_the_first_subchapter(): void
    {
        $this->get('/baca')->assertRedirect('/baca/01-2-masalah-utama');
    }

    #[DataProvider('freeSlugs')]
    public function test_each_subchapter_of_chapter_one_is_readable_without_an_account(string $slug): void
    {
        $this->get('/baca/'.$slug)
            ->assertOk()
            ->assertSee('Bab 1 gratis')
            ->assertSee('<article class="prose">', false);
    }

    public static function freeSlugs(): array
    {
        return [
            ['01-2-masalah-utama'],
            ['02-solusi-parsial'],
            ['03-halusinasi-meruntuhkan-kredibilitas-akademik'],
            ['04-hilang-memori-tak-berarti'],
        ];
    }

    public function test_later_chapters_are_not_public(): void
    {
        $this->get('/baca/05-taaruf')->assertNotFound();
        $this->get('/baca/19-cara-instalasi-arti-framework')->assertNotFound();
    }

    public function test_only_free_images_are_served(): void
    {
        $this->get('/baca/04-hilang-memori-tak-berarti/images/pemintal-benang.png')->assertOk();
        $this->get('/baca/05-taaruf/images/anything.png')->assertNotFound();
        $this->get('/baca/04-hilang-memori-tak-berarti/images/missing.png')->assertNotFound();
    }

    public function test_links_to_locked_subchapters_become_plain_text(): void
    {
        $response = $this->get('/baca/07-masih-ada-yang-kurang');

        $response->assertNotFound();

        $html = $this->get('/baca/02-solusi-parsial')->getContent();

        $this->assertStringNotContainsString('[[', $html);
        $this->assertStringNotContainsString('/learn/', $html);
    }

    public function test_the_last_free_page_offers_the_purchase_and_earlier_ones_link_onwards(): void
    {
        $this->get('/baca/01-2-masalah-utama')->assertSee('/baca/02-solusi-parsial', false);

        $this->get('/baca/04-hilang-memori-tak-berarti')
            ->assertSee('Sampai di sini bab 1 gratis.')
            ->assertSee('Lihat harga');
    }

    #[DataProvider('freeSlugs')]
    public function test_every_free_page_has_the_email_sign_up_tagged_with_its_page_number(string $slug): void
    {
        $position = array_search($slug, array_column(app(\App\Http\Controllers\FreeChapterController::class)->subchapters(), 'slug')) + 1;

        $this->get('/baca/'.$slug)
            ->assertSee('data-subscribe-card', false)
            ->assertSee('name="source" value="baca-'.$position.'"', false)
            ->assertSee('action="'.route('subscribe.store').'"', false);
    }

    public function test_page_two_has_its_own_hook_and_the_last_page_keeps_both_the_sign_up_and_the_buy_button(): void
    {
        $this->get('/baca/02-solusi-parsial')
            ->assertSee('Dua solusi, dua kelemahan baru')
            ->assertSee('Tidak sekarang');

        $this->get('/baca/04-hilang-memori-tak-berarti')
            ->assertSee('Kabari saya bila ada kelanjutannya')
            ->assertDontSee('Tidak sekarang')
            ->assertSee('Beli sekarang');
    }

    public function test_free_pages_are_in_the_sitemap_and_the_landing_links_to_the_chapter(): void
    {
        $this->get('/sitemap.xml')->assertSee(url('/baca/01-2-masalah-utama'), false);

        // The hero button now jumps to the free section, whose first card links to the chapter.
        $this->get('/')->assertSee('Baca bab 1')->assertSee(route('free.start'), false);
    }
}
