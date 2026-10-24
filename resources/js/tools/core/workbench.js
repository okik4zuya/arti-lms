import { copyText } from './clipboard.js';
import { formatForAi, hasFail, paintReport } from './errors.js';

// Wires the shared workbench markup (partials/workbench-start): run, examples, clear, report, "Salin kesalahan".
// `tool.run(text)` returns { issues, ok } and may show the preview itself; `tool.examples[key]()` returns text.
export function mountWorkbench(tool) {
    const root = document.querySelector('[data-workbench]');
    if (!root) return null;
    const input = root.querySelector('[data-wb-input]');
    const report = root.querySelector('[data-wb-report]');
    const copyStatus = root.querySelector('[data-wb-copy-status]');
    const preview = document.querySelector('[data-wb-preview]');
    const downloads = document.querySelector('[data-wb-downloads]');
    let last = [];

    const show = (issues, ok) => {
        last = issues;
        paintReport(report, issues);
        if (preview) preview.hidden = !ok;
        if (downloads) downloads.hidden = !ok;
        copyStatus.textContent = '';
    };

    const run = async () => {
        const text = input.value;
        if (!text.trim()) {
            show([], false);
            return;
        }
        try {
            const res = await tool.run(text);
            show(res.issues, res.ok);
        } catch (e) {
            show([{ level: 'fail', code: 'E_INTERNAL', where: '', message: String(e && e.message ? e.message : e) }], false);
        }
    };

    root.querySelector('[data-wb-run]').addEventListener('click', run);
    root.querySelector('[data-wb-clear]').addEventListener('click', () => {
        input.value = '';
        show([], false);
        input.focus();
    });
    root.querySelector('[data-wb-example]').addEventListener('change', async (ev) => {
        const key = ev.target.value;
        if (!key || !tool.examples[key]) return;
        input.value = await tool.examples[key]();
        ev.target.value = '';
        run();
    });
    root.querySelector('[data-wb-copy-errors]').addEventListener('click', async () => {
        const text = formatForAi(tool.contract, last);
        copyStatus.textContent = (await copyText(text)) ? 'Tersalin. Tempel ke chat AI.' : 'Gagal menyalin; blok teks bisa dipilih manual.';
    });

    return { root, input, run, hasFail: () => hasFail(last) };
}
