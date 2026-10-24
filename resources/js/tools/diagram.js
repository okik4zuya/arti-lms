import { mountWorkbench } from './core/workbench.js';
import { canvasFits, canvasToBlob, downloadBlob, downloadText } from './core/download.js';
import { ok, warn } from './core/errors.js';
import { validateDiagram, DEFAULTS, CONTRACT } from './lib/diagram-spec.js';
import { layoutDiagram } from './lib/diagram-layout.js';
import { toDrawio, toSvg } from './lib/diagram-svg.js';
import studi from './examples/diagram-studi.json?raw';
import prisma from './examples/diagram-prisma.json?raw';
import metode from './examples/diagram-metode.json?raw';

const frame = document.querySelector('[data-wb-frame]');
const meta = document.querySelector('[data-wb-meta]');
let current = null; // { layout, svg }

const ctx = document.createElement('canvas').getContext('2d');
ctx.font = `${DEFAULTS.fontSize}px Arial, Helvetica, sans-serif`;
const measure = (text) => ctx.measureText(text).width;

async function run(text) {
    current = null;
    const { spec, issues } = validateDiagram(text);
    if (!spec) return { issues, ok: false };

    const layout = layoutDiagram(spec, measure);
    const svg = toSvg(layout);
    current = { layout, svg };
    frame.innerHTML = svg;
    meta.textContent = `${spec.nodes.length} node, ${spec.edges.length} panah, ${layout.width} x ${layout.height} px`;
    if (spec.nodes.length > 30) issues.push(warn('W_BIG', 'diagram', 'lebih dari 30 node; pertimbangkan memecahnya agar terbaca di halaman jurnal.'));
    issues.push(ok('spesifikasi valid dan tata letak selesai'));
    return { issues, ok: true };
}

mountWorkbench({
    contract: CONTRACT,
    run,
    examples: { studi: async () => studi, prisma: async () => prisma, metode: async () => metode },
});

document.querySelector('[data-dl="drawio"]').addEventListener('click', () => {
    if (current) downloadText('diagram.drawio', toDrawio(current.layout), 'application/vnd.jgraph.mxfile');
});
document.querySelector('[data-dl="svg"]').addEventListener('click', () => {
    if (current) downloadText('diagram.svg', current.svg, 'image/svg+xml');
});
document.querySelector('[data-dl="png"]').addEventListener('click', async () => {
    if (!current) return;
    const { width, height } = current.layout;
    const scale = 2;
    if (!canvasFits(width * scale, height * scale)) {
        alert('Diagram terlalu besar untuk dijadikan PNG. Unduh SVG atau .drawio.');
        return;
    }
    const img = new Image();
    await new Promise((resolve, reject) => {
        img.onload = resolve;
        img.onerror = reject;
        img.src = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(current.svg);
    });
    const canvas = document.createElement('canvas');
    canvas.width = width * scale;
    canvas.height = height * scale;
    canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);
    downloadBlob('diagram.png', await canvasToBlob(canvas));
});
