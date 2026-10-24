// Page size and source checks for the composite tool. Pure functions (no DOM), unit-tested with `node --test`.
// Meta tags and defaults mirror tools/arti-compose/compose.py so a source written here also renders there.
import { fail, warn } from '../core/errors.js';

export const UNITS_PX = { px: 1, mm: 96 / 25.4, cm: 96 / 2.54, in: 96, pt: 96 / 72 };

export function parseMeta(html) {
    const meta = {};
    for (const m of html.matchAll(/<meta\s+name="arti:([a-z-]+)"\s+content="([^"]*)"/g)) meta[m[1]] = m[2].trim();
    return meta;
}

/** '190mm' -> { px, mm, unit }. A bare number is px. Throws on anything else. */
export function parseLen(s) {
    const m = /^\s*([0-9.]+)\s*(mm|cm|in|pt|px)?\s*$/.exec(s || '');
    if (!m) throw new Error(`ukuran "${s}" tidak terbaca (contoh: 190mm, 8in, 1328px)`);
    const unit = m[2] || 'px';
    const px = parseFloat(m[1]) * UNITS_PX[unit];
    return { px, mm: (px * 25.4) / 96, unit };
}

/** Returns { spec, issues }. spec is null when the size is missing or unreadable. */
export function resolveSpec(html) {
    const issues = [];
    const meta = parseMeta(html);
    if (!meta.width || !meta.height) {
        issues.push(fail('E_SIZE_MISSING', 'meta', 'tambahkan <meta name="arti:width" content="190mm"> dan <meta name="arti:height" content="110mm"> (atau satuan px) di <head>.'));
        return { spec: null, issues };
    }
    let w, h;
    try {
        w = parseLen(meta.width);
        h = parseLen(meta.height);
    } catch (e) {
        issues.push(fail('E_SIZE_UNREADABLE', 'meta', e.message));
        return { spec: null, issues };
    }
    const mode = meta.mode || (w.unit === 'px' ? 'pixel' : 'print');
    if (mode !== 'print' && mode !== 'pixel') {
        issues.push(fail('E_MODE', 'meta arti:mode', `"${mode}" tidak dikenal; pakai print atau pixel.`));
        return { spec: null, issues };
    }
    const spec = {
        mode,
        wCss: meta.width, hCss: meta.height,
        wPx: w.px, hPx: h.px, wMm: w.mm, hMm: h.mm,
        dpi: parseFloat(meta.dpi) || 600,
        minPt: parseFloat(meta['min-pt']) || 7,
        minPx: parseFloat(meta['min-px']) || 24,
    };
    if (!/^\s*<!doctype html/i.test(html)) {
        issues.push(warn('W_DOCTYPE', 'awal berkas', 'tidak ada <!doctype html>; browser memakai mode quirks sehingga tata letak bisa berbeda.'));
    }
    return { spec, issues };
}

const STATIC_TAGS = ['script', 'iframe', 'object', 'embed', 'link', 'base', 'form', 'audio', 'video'];

function snippet(s) {
    return s.replace(/\s+/g, ' ').slice(0, 60);
}

/** Source rules: nothing that runs, nothing that loads from outside. Gem output may only use inline SVG and CSS. */
export function staticCheck(html) {
    const issues = [];
    for (const tag of STATIC_TAGS) {
        if (new RegExp('<' + tag + '[\\s>/]', 'i').test(html)) {
            const code = tag === 'script' ? 'E_SCRIPT' : 'E_TAG';
            issues.push(fail(code, `<${tag}>`, tag === 'script'
                ? 'skrip tidak diizinkan. Minta AI menghapus semua <script> dan membuat gambar hanya dari HTML, CSS, dan SVG.'
                : `elemen <${tag}> tidak diizinkan di jalur gratis. Pakai HTML, CSS, dan SVG inline saja.`));
        }
    }
    if (/<meta[^>]+http-equiv/i.test(html)) issues.push(fail('E_TAG', '<meta http-equiv>', 'tidak diizinkan.'));

    for (const m of html.matchAll(/<[a-z][^>]*?\s(on[a-z]+)\s*=/gi)) {
        issues.push(fail('E_HANDLER', m[1], 'atribut kejadian (onclick dan sejenisnya) tidak diizinkan.'));
        break;
    }
    const external = new Set();
    for (const m of html.matchAll(/\b(?:src|href|xlink:href|poster|srcset)\s*=\s*["']?\s*([^"'\s>]+)/gi)) {
        if (!/^(data:|#)/i.test(m[1])) external.add(m[1]);
    }
    for (const m of html.matchAll(/url\(\s*["']?\s*([^)"']+)/gi)) {
        if (!/^(data:|#)/i.test(m[1])) external.add(m[1]);
    }
    if (/@import/i.test(html)) external.add('@import');
    for (const url of external) {
        issues.push(fail('E_EXTERNAL', snippet(url), 'berkas atau tautan luar tidak diizinkan. Gambar harus SVG inline atau data: URI; minta AI menggambar ulang sebagai <svg>.'));
    }
    return issues;
}

/** Copy of the source with page size and margins forced to the spec (what compose.py's inject() does). */
export function injectPage(html, spec) {
    const css = `:root{--page-w:${spec.wCss};--page-h:${spec.hCss};}html,body{margin:0;}@page{size:${spec.wCss} ${spec.hCss};margin:0;}html{width:${spec.wCss};}`;
    const style = `<style id="arti-compose-page">${css}</style>\n`;
    return html.includes('</head>') ? html.replace('</head>', style + '</head>') : style + html;
}

/** PNG size in device pixels for the spec: exact px in pixel mode, mm at dpi in print mode. */
export function pngSize(spec) {
    if (spec.mode === 'pixel') return { w: Math.round(spec.wPx), h: Math.round(spec.hPx), scale: 1 };
    const scale = spec.dpi / 96;
    return { w: Math.round(spec.wPx * scale), h: Math.round(spec.hPx * scale), scale };
}
