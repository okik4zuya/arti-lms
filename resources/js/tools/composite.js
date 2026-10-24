import { mountWorkbench } from './core/workbench.js';
import { downloadBlob, downloadText } from './core/download.js';
import { ok } from './core/errors.js';
import { resolveSpec, staticCheck } from './lib/composite-spec.js';
import { lintDocument } from './lib/composite-lint.js';
import { capturePng, mountPreview, printPdf } from './lib/composite-render.js';
import multiPanel from './examples/composite-multi-panel.html?raw';
import graphicalAbstract from './examples/composite-graphical-abstract.html?raw';

let current = null; // { html, spec, doc }

const frame = document.querySelector('[data-wb-frame]');
const meta = document.querySelector('[data-wb-meta]');

async function run(text) {
    current = null;
    const html = text.trim();
    const { spec, issues } = resolveSpec(html);
    const blocked = staticCheck(html);
    issues.push(...blocked);
    if (!spec || blocked.length) return { issues, ok: false };

    // The preview must be visible while it is measured: a hidden iframe has no layout and the lint would see nothing.
    document.querySelector('[data-wb-preview]').hidden = false;
    const { doc } = await mountPreview(frame, html, spec);
    issues.push(...lintDocument(doc, spec));
    const failed = issues.some((i) => i.level === 'fail');
    current = { html, spec, doc };
    meta.textContent = spec.mode === 'pixel'
        ? `${Math.round(spec.wPx)} x ${Math.round(spec.hPx)} px, mode piksel`
        : `${spec.wMm.toFixed(0)} x ${spec.hMm.toFixed(0)} mm, ${spec.dpi} dpi, mode cetak`;
    if (!failed) issues.push(ok('siap diunduh'));
    return { issues, ok: true };
}

mountWorkbench({
    contract: 'composite',
    run,
    examples: {
        'multi-panel': async () => multiPanel,
        'graphical-abstract': async () => graphicalAbstract,
    },
});

document.querySelector('[data-dl="png"]').addEventListener('click', async () => {
    if (!current) return;
    try {
        const { blob, w, h } = await capturePng(current.doc, current.spec);
        downloadBlob(`gambar-${w}x${h}.png`, blob);
    } catch (e) {
        alert(e.message);
    }
});
document.querySelector('[data-dl="pdf"]').addEventListener('click', () => {
    if (!current) return;
    if (!printPdf(current.html, current.spec)) alert('Browser memblokir tab baru. Izinkan pop-up untuk situs ini lalu coba lagi.');
});
document.querySelector('[data-dl="html"]').addEventListener('click', () => {
    if (current) downloadText('gambar.html', current.html, 'text/html');
});

