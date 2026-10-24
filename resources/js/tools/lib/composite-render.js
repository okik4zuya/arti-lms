// Browser side of the composite tool: sandboxed preview, PNG capture and the print route to PDF.
// Gem output never runs scripts: the iframe has no allow-scripts, and staticCheck() already refused <script>.
import { injectPage, pngSize } from './composite-spec.js';
import { canvasFits, canvasToBlob } from '../core/download.js';

/** Render `html` into a sandboxed iframe at the spec's size and resolve with { iframe, doc } once laid out. */
export function mountPreview(frameBox, html, spec) {
    frameBox.textContent = '';
    const iframe = document.createElement('iframe');
    iframe.setAttribute('sandbox', 'allow-same-origin');
    iframe.setAttribute('title', 'Pratinjau gambar');
    iframe.style.width = spec.wPx + 'px';
    iframe.style.height = spec.hPx + 'px';
    const scale = Math.min(1, (frameBox.clientWidth || spec.wPx) / spec.wPx);
    iframe.style.transform = `scale(${scale})`;
    frameBox.style.height = Math.ceil(spec.hPx * scale) + 'px';

    return new Promise((resolve) => {
        iframe.addEventListener('load', async () => {
            const doc = iframe.contentDocument;
            try { await doc.fonts.ready; } catch { /* no font API */ }
            // let images and layout settle
            await new Promise((r) => setTimeout(r, 50));
            resolve({ iframe, doc });
        }, { once: true });
        iframe.srcdoc = injectPage(html, spec);
        frameBox.append(iframe);
    });
}

/** PNG blob of the iframe document: exact px in pixel mode, mm at dpi in print mode. */
export async function capturePng(doc, spec) {
    const { w, h, scale } = pngSize(spec);
    if (!canvasFits(w, h)) {
        throw new Error(`PNG ${w} x ${h} px terlalu besar untuk browser. Kecilkan ukurannya atau turunkan arti:dpi.`);
    }
    const xhtml = new XMLSerializer().serializeToString(doc.documentElement);
    const W = Math.round(spec.wPx), H = Math.round(spec.hPx);
    const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="${W}" height="${H}" viewBox="0 0 ${W} ${H}">`
        + `<foreignObject x="0" y="0" width="${W}" height="${H}">${xhtml}</foreignObject></svg>`;
    const img = new Image();
    img.decoding = 'sync';
    await new Promise((resolve, reject) => {
        img.onload = resolve;
        img.onerror = () => reject(new Error('Browser gagal merender gambar ke PNG. Coba Chrome, Edge, atau Firefox.'));
        img.src = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svg);
    });
    const canvas = document.createElement('canvas');
    canvas.width = w;
    canvas.height = h;
    const ctx = canvas.getContext('2d');
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, w, h);
    ctx.drawImage(img, 0, 0, w, h);
    return { blob: await canvasToBlob(canvas), w, h, scale };
}

/** Open the page in a new tab at its real size and start the print dialog (Simpan sebagai PDF). */
export function printPdf(html, spec) {
    const printable = injectPage(html, spec).replace(
        /<\/body>|$/,
        (m) => `<script>addEventListener('load',function(){setTimeout(function(){print()},300)})</script>${m}`,
    );
    const url = URL.createObjectURL(new Blob([printable], { type: 'text/html;charset=utf-8' }));
    const win = window.open(url, '_blank');
    setTimeout(() => URL.revokeObjectURL(url), 60000);
    return !!win;
}
