// Layout lint of the rendered source, run by the parent page on the sandboxed iframe's document.
// Ported from LINT_JS in tools/arti-compose/compose.py; text size and raster dpi are measured from the DOM
// because the PDF checks (pdfminer) cannot run in a browser.
import { fail, warn, ok } from '../core/errors.js';

function sel(e) {
    if (e.id) return '#' + e.id;
    let s = e.tagName.toLowerCase();
    if (typeof e.className === 'string' && e.className.trim()) s += '.' + e.className.trim().split(/\s+/).join('.');
    return s;
}

const words = (t) => t.trim().split(/\s+/).filter(Boolean).length;

export function lintDocument(doc, spec) {
    const win = doc.defaultView;
    const issues = [];
    const W = spec.wPx, H = spec.hPx;
    const ignored = (e) => e.closest && e.closest('[data-lint-ignore]');
    const overflow = [], svgText = [], img = [], outside = [], sentences = [];
    const small = new Map();
    const floorPx = spec.mode === 'pixel' ? spec.minPx : (spec.minPt * 96) / 72;
    const unit = spec.mode === 'pixel' ? 'px' : 'pt';
    const toUnit = (px) => (spec.mode === 'pixel' ? px : (px * 72) / 96);
    let chars = 0;
    let rasterLow = null;

    const noteSize = (px, text) => {
        chars += text.length;
        if (px < floorPx - 0.05) {
            const key = toUnit(px).toFixed(1);
            const cur = small.get(key) || { n: 0, sample: '' };
            cur.n += 1;
            if (cur.sample.length < 28) cur.sample = (cur.sample + ' ' + text).trim().slice(0, 28);
            small.set(key, cur);
        }
    };

    doc.querySelectorAll('body *').forEach((e) => {
        if (ignored(e)) return;
        if (e.closest('svg') && e.tagName.toLowerCase() !== 'svg') return;
        const cs = win.getComputedStyle(e);
        if (cs.display === 'none') return;
        if (e.tagName === 'IMG') {
            if (!(e.complete && e.naturalWidth > 0)) img.push(sel(e) + ' src=' + (e.getAttribute('src') || '').slice(0, 30));
            else if (spec.mode === 'print' && /^data:/.test(e.src)) {
                const inches = e.getBoundingClientRect().width / 96;
                if (inches > 0) {
                    const dpi = e.naturalWidth / inches;
                    if (rasterLow === null || dpi < rasterLow) rasterLow = dpi;
                }
            }
            return;
        }
        if (cs.display !== 'inline' && e.clientWidth > 0 && e.clientHeight > 0
            && (e.scrollWidth > e.clientWidth + 1 || e.scrollHeight > e.clientHeight + 1)) {
            overflow.push(`${sel(e)} isi ${e.scrollWidth}x${e.scrollHeight} > kotak ${e.clientWidth}x${e.clientHeight}`);
        }
        const own = Array.from(e.childNodes).filter((n) => n.nodeType === 3 && n.textContent.trim()).map((n) => n.textContent.trim()).join(' ');
        if (own) {
            const b = e.getBoundingClientRect();
            if (b.left < -1 || b.top < -1 || b.right > W + 1 || b.bottom > H + 1) outside.push(`${sel(e)} teks di luar halaman`);
            noteSize(parseFloat(cs.fontSize), own);
            if (spec.mode === 'pixel' && words(own) >= 9 && !/^(H[1-6]|TITLE)$/.test(e.tagName)) sentences.push(`"${own.slice(0, 40)}"`);
        }
    });

    doc.querySelectorAll('svg text').forEach((t) => {
        if (ignored(t)) return;
        const s = t.ownerSVGElement;
        if (!s) return;
        const a = t.getBoundingClientRect(), b = s.getBoundingClientRect();
        if (a.width && (a.left < b.left - 1 || a.right > b.right + 1 || a.top < b.top - 1 || a.bottom > b.bottom + 1)) {
            svgText.push(`"${t.textContent.trim().slice(0, 40)}" melewati batas <svg> (terpotong)`);
        }
        let scale = 1;
        try { scale = t.getScreenCTM().a || 1; } catch { /* detached */ }
        const own = t.textContent.trim();
        if (own) noteSize(parseFloat(win.getComputedStyle(t).fontSize) * scale, own);
    });

    const body = doc.body;
    if (body.scrollWidth > W + 1 || body.scrollHeight > H + 1) {
        issues.push(fail('E_PAGE_OVERFLOW', 'halaman', `isi ${body.scrollWidth} x ${body.scrollHeight} px melebihi halaman ${Math.round(W)} x ${Math.round(H)} px. Kecilkan isinya atau naikkan arti:height.`));
    }
    const groups = [
        [overflow, 'E_OVERFLOW', 'isi melebihi kotaknya'],
        [svgText, 'E_SVG_TEXT_CLIPPED', 'teks SVG terpotong'],
        [img, 'E_IMG_BROKEN', 'gambar gagal dimuat'],
        [outside, 'E_OUTSIDE', 'di luar halaman'],
    ];
    for (const [list, code, label] of groups) {
        for (const item of list.slice(0, 6)) issues.push(fail(code, label, item));
        if (list.length > 6) issues.push(fail(code, label, `dan ${list.length - 6} lainnya`));
    }
    if (sentences.length) {
        issues.push(warn('W_EXPLANATORY_TEXT', 'teks', `graphical abstract tidak boleh berisi kalimat penjelas (${sentences.slice(0, 3).join(', ')}). Biarkan gambar yang bercerita; sisakan judul, label 1-2 kata, angka.`));
    }
    if (chars) {
        if (small.size) {
            const detail = [...small.entries()].sort((x, y) => x[0] - y[0]).map(([k, v]) => `${k} ${unit} x${v.n} ("${v.sample}")`).join('; ');
            const floor = spec.mode === 'pixel' ? spec.minPx : spec.minPt;
            issues.push(warn('W_TEXT_SMALL', 'ukuran huruf', `di bawah ${floor} ${unit} pada ukuran akhir: ${detail}`));
        }
    } else {
        issues.push(warn('W_NO_TEXT', 'teks', 'tidak ada teks yang terbaca di gambar.'));
    }
    if (rasterLow !== null && rasterLow < 300) {
        issues.push(warn('W_RASTER_DPI', 'gambar raster', `resolusi efektif ${Math.round(rasterLow)} dpi (< 300). Pakai gambar sumber yang lebih besar.`));
    }
    if (!issues.some((i) => i.level === 'fail')) issues.unshift(ok('tata letak: tidak ada teks terpotong atau meluber'));
    if (chars && !small.size) issues.push(ok(`semua teks >= ${spec.mode === 'pixel' ? spec.minPx : spec.minPt} ${unit} pada ukuran akhir`));
    return issues;
}
