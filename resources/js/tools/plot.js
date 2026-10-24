import { mountWorkbench } from './core/workbench.js';
import { canvasFits, canvasToBlob, downloadBlob, downloadText } from './core/download.js';
import { ok, warn } from './core/errors.js';
import { compilePlot, makeView, minTextPt, validatePlot, CONTRACT } from './lib/plot-spec.js';
import { csvToValues } from './lib/csv.js';
import batang from './examples/plot-batang.json?raw';
import kelompok from './examples/plot-kelompok.json?raw';
import garis from './examples/plot-garis.json?raw';
import sebar from './examples/plot-sebar.json?raw';

const frame = document.querySelector('[data-wb-frame]');
const meta = document.querySelector('[data-wb-meta]');
const widthSelect = document.querySelector('[data-wb-width]');
let view = null;
let wb = null;

const widthMm = () => parseInt(widthSelect.value, 10);

async function run(text) {
    view?.finalize();
    view = null;
    const { spec, issues } = validatePlot(text);
    if (!spec) return { issues, ok: false };
    const { vegaSpec, issues: compileIssues } = compilePlot(spec);
    issues.push(...compileIssues);
    if (!vegaSpec) return { issues, ok: false };

    frame.textContent = '';
    view = makeView(vegaSpec, { renderer: 'svg', container: frame }).hover();
    try {
        await view.runAsync();
    } catch (e) {
        view = null;
        issues.push({ level: 'fail', code: 'E_RENDER', where: 'Vega', message: String(e.message || e) });
        return { issues, ok: false };
    }
    const svg = await view.toSVG();
    const w = /<svg[^>]*\swidth="([0-9.]+)"[^>]*\sheight="([0-9.]+)"/.exec(svg);
    meta.textContent = w ? `${Math.round(w[1])} x ${Math.round(w[2])} px` : '';
    const pt = minTextPt(svg, widthMm());
    if (pt !== null && pt < 7) {
        issues.push(warn('W_TEXT_SMALL', 'ukuran huruf', `teks terkecil ${pt.toFixed(1)} pt pada lebar ${widthMm()} mm (batas 7 pt). Pilih lebar 170 mm, atau minta AI membuat grafik lebih kecil (width ~300).`));
    } else if (pt !== null) {
        issues.push(ok(`teks terkecil ${pt.toFixed(1)} pt pada lebar ${widthMm()} mm`));
    }
    issues.push(ok('grafik berhasil dibuat'));
    return { issues, ok: true };
}

wb = mountWorkbench({
    contract: CONTRACT,
    run,
    examples: { batang: async () => batang, kelompok: async () => kelompok, garis: async () => garis, sebar: async () => sebar },
});

widthSelect.addEventListener('change', () => { if (view) wb.run(); });

document.querySelector('[data-dl="svg"]').addEventListener('click', async () => {
    if (view) downloadText('grafik.svg', await view.toSVG(), 'image/svg+xml');
});
document.querySelector('[data-dl="png"]').addEventListener('click', async () => {
    if (!view) return;
    const svg = await view.toSVG();
    const w = parseFloat(/<svg[^>]*\swidth="([0-9.]+)"/.exec(svg)[1]);
    const target = Math.round((widthMm() / 25.4) * 300);
    const scale = target / w;
    const h = parseFloat(/<svg[^>]*\sheight="([0-9.]+)"/.exec(svg)[1]) * scale;
    if (!canvasFits(target, h)) {
        alert('Grafik terlalu besar untuk dijadikan PNG. Unduh SVG.');
        return;
    }
    const canvas = await view.toCanvas(scale);
    downloadBlob(`grafik-${widthMm()}mm-300dpi.png`, await canvasToBlob(canvas));
});

// CSV helper
const csvIn = document.querySelector('[data-csv-in]');
const csvOut = document.querySelector('[data-csv-out]');
document.querySelector('[data-csv-run]')?.addEventListener('click', () => {
    const { values, error } = csvToValues(csvIn.value);
    csvOut.value = error ? 'Kesalahan: ' + error : '"data": { "values": ' + JSON.stringify(values) + ' }';
});
