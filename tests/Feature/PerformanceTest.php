<?php

namespace Tests\Feature;

use Tests\TestCase;

class PerformanceTest extends TestCase
{
    public function test_fonts_are_self_hosted_with_no_third_party_request(): void
    {
        foreach (['/', '/fitur', '/kontak', '/login'] as $path) {
            $this->get($path)->assertDontSee('fonts.bunny.net', false);
        }

        foreach (['400', '600', '700'] as $weight) {
            $this->assertFileExists(public_path("fonts/figtree/figtree-latin-{$weight}-normal.woff2"));
        }
        $this->assertFileExists(public_path('fonts/figtree/OFL.txt'));

        $this->get('/')->assertSee('fonts/figtree/figtree-latin-400-normal.woff2', false);
    }

    public function test_header_logo_is_a_small_sized_image_with_dimensions(): void
    {
        $this->get('/')->assertSee('images/wordmark-site.webp', false)->assertSee('width="73" height="32"', false);

        $this->assertLessThan(8 * 1024, filesize(public_path('images/wordmark-site.webp')));
        $this->assertLessThan(8 * 1024, filesize(public_path('favicon.ico')));
    }

    public function test_content_visibility_is_switched_off_for_anchor_jumps(): void
    {
        $this->get('/')->assertSee("classList.add('no-cv')", false);

        $css = file_get_contents(base_path('resources/css/site.css'));
        $this->assertStringContainsString('html:not(.no-cv) .landing-section', $css);
    }

    public function test_the_feature_animation_loads_only_when_it_scrolls_into_view(): void
    {
        $this->get('/fitur')
            ->assertSee('preload="none"', false)
            ->assertSee('IntersectionObserver', false);
    }

    public function test_no_unused_landing_images_are_left_in_public(): void
    {
        $referenced = collect(glob(resource_path('views/site/*.blade.php')))
            ->map(fn ($file) => file_get_contents($file))
            ->implode("\n");

        foreach (glob(public_path('images/landing/*.{webp,png}'), GLOB_BRACE) as $file) {
            $name = basename($file);
            $this->assertStringContainsString($name, $referenced.file_get_contents(resource_path('views/layouts/site.blade.php')), "{$name} is shipped but not used");
        }
    }
}
