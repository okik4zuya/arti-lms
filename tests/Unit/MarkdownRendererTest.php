<?php

namespace Tests\Unit;

use App\Support\MarkdownRenderer;
use PHPUnit\Framework\TestCase;

class MarkdownRendererTest extends TestCase
{
    public function test_renders_gfm_tables(): void
    {
        $html = (new MarkdownRenderer)->toHtml("| a | b |\n|---|---|\n| 1 | 2 |\n");

        $this->assertStringContainsString('<table>', $html);
    }

    public function test_keeps_style_blocks_but_blocks_scripts_and_iframes(): void
    {
        $html = (new MarkdownRenderer)->toHtml("<style>.x{color:red}</style>\n\n<script>alert(1)</script>\n\n<iframe src=\"x\"></iframe>\n");

        $this->assertStringContainsString('<style>', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('<iframe', $html);
    }

    public function test_rewrites_only_relative_image_references(): void
    {
        $renderer = new MarkdownRenderer;
        $html = $renderer->toHtml("![a](images/one.png)\n\n![b](./images/two.png)\n\n![c](https://cdn.test/x.png)\n\n![d](/abs/y.png)\n");

        $out = $renderer->rewriteImages($html, fn (string $file) => "/served/{$file}");

        $this->assertStringContainsString('src="/served/one.png"', $out);
        $this->assertStringContainsString('src="/served/two.png"', $out);
        $this->assertStringContainsString('src="https://cdn.test/x.png"', $out);
        $this->assertStringContainsString('src="/abs/y.png"', $out);
    }
}
