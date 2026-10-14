<?php

namespace App\Support;

use League\CommonMark\GithubFlavoredMarkdownConverter;

/**
 * The one Markdown pipeline for flat-file content (course subchapters and articles).
 * Raw HTML is allowed so a document can carry its own <style>; the tags below stay blocked.
 */
class MarkdownRenderer
{
    public function toHtml(string $markdown): string
    {
        $converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'allow',
            'disallowed_raw_html' => [
                'disallowed_tags' => ['title', 'textarea', 'xmp', 'iframe', 'noembed', 'noframes', 'script', 'plaintext'],
            ],
        ]);

        return $converter->convert($markdown)->getContent();
    }

    /**
     * Point relative `images/<file>` references at a served URL.
     *
     * @param  callable(string): string  $urlFor  receives the file name inside images/
     */
    public function rewriteImages(string $html, callable $urlFor): string
    {
        return preg_replace_callback(
            '/<img\s+([^>]*?)src="(?!https?:\/\/|\/)(?:\.\/)?images\/([^"]+)"/i',
            fn (array $matches) => sprintf('<img %ssrc="%s"', $matches[1], $urlFor($matches[2])),
            $html
        );
    }
}
