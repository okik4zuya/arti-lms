<?php

namespace App\Support;

/** Plain-text twin of an HTML email, so every message has a readable text part. */
final class MailText
{
    public static function fromHtml(string $html): string
    {
        // Only the body: the layout has a <style> block that must not leak into the text part.
        $html = preg_replace('#<(style|head|script)\b.*?</\1>#is', '', $html) ?? $html;
        // A link becomes "text (url)", unless the text already is the url.
        $html = preg_replace_callback('#<a\s[^>]*href="([^"]+)"[^>]*>(.*?)</a>#is', function (array $m) {
            $text = trim(strip_tags($m[2]));
            $url = html_entity_decode($m[1]);

            return $text === '' || $text === $url ? $url : "{$text} ({$url})";
        }, $html) ?? $html;
        $html = preg_replace('#<li[^>]*>#i', "\n- ", $html) ?? $html;
        $html = preg_replace('#<br\s*/?>|</(p|h[1-6]|div|tr|ul|ol|blockquote)>#i', "\n\n", $html) ?? $html;

        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/^[ \t]+|[ \t]+$/m', '', $text) ?? $text; // template indentation is not content
        $text = preg_replace('/\n{3,}/', "\n\n", $text) ?? $text;

        return trim($text)."\n";
    }
}
