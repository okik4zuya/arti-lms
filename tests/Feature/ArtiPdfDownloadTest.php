<?php

namespace Tests\Feature;

use App\Models\DownloadCount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtiPdfDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_download_redirects_to_the_r2_installer_and_counts_it(): void
    {
        config(['site.artipdf_download_url' => 'https://cdn-arti.invait.id/downloads/artipdf/ArtiPDFSetup-1.0.7.exe']);

        $this->get(route('artipdf.unduh'))
            ->assertRedirect('https://cdn-arti.invait.id/downloads/artipdf/ArtiPDFSetup-1.0.7.exe')
            ->assertHeader('X-Robots-Tag', 'noindex');
        $this->get(route('artipdf.unduh'))->assertRedirect();

        $this->assertSame(2, DownloadCount::where('name', 'artipdf')->whereDate('day', today())->sole()->downloads);
    }

    public function test_the_defaults_point_at_the_uploaded_installer_of_the_listed_version(): void
    {
        $this->assertSame(
            'https://cdn-arti.invait.id/downloads/artipdf/ArtiPDFSetup-'.config('site.artipdf_version').'.exe',
            config('site.artipdf_download_url')
        );
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', config('site.artipdf_sha256'));
    }

    public function test_without_an_r2_link_it_falls_back_to_the_github_release(): void
    {
        config(['site.artipdf_download_url' => null]);

        $this->get(route('artipdf.unduh'))->assertRedirect(config('site.artipdf_url'));
    }

    public function test_a_new_day_starts_a_new_count(): void
    {
        $this->get(route('artipdf.unduh'));
        $this->travel(1)->days();
        $this->get(route('artipdf.unduh'));

        $this->assertSame(2, DownloadCount::count());
        $this->assertSame(2, (int) DownloadCount::sum('downloads'));
    }

    public function test_the_page_links_to_the_counting_route_and_has_the_thank_you_dialog(): void
    {
        $this->get('/artipdf')
            ->assertOk()
            ->assertSee(route('artipdf.unduh'), false)
            ->assertSee('id="download-thanks"', false)
            ->assertSee('data-download', false)
            ->assertSee('name="source" value="artipdf"', false)
            ->assertSee('More info');
    }

    public function test_the_github_link_stays_and_the_checksum_shows_only_when_set(): void
    {
        config(['site.artipdf_sha256' => null]);
        $this->get('/artipdf')->assertSee(config('site.artipdf_url'), false)->assertDontSee('SHA-256');

        config(['site.artipdf_sha256' => str_repeat('ab', 32)]);
        $this->get('/artipdf')->assertSee('SHA-256')->assertSee(str_repeat('ab', 32));
    }

    public function test_the_button_opens_a_new_tab_only_when_it_leads_to_the_github_page(): void
    {
        config(['site.artipdf_download_url' => null]);
        $this->get('/artipdf')->assertSee('target="_blank"', false);

        config(['site.artipdf_download_url' => 'https://cdn.test/x.exe']);
        $this->get('/artipdf')->assertDontSee('data-download target="_blank"', false);
    }
}
