// Pure-module tests for the free tools: `npm run test:tools` (Node's built-in runner, no extra dependency).
import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';

import { formatForAi, fail, warn, ok } from './core/errors.js';
import { parseLen, resolveSpec, staticCheck, injectPage, pngSize } from './lib/composite-spec.js';
import { validateDiagram, stripFence, STYLES } from './lib/diagram-spec.js';
import { layoutDiagram, wrapText } from './lib/diagram-layout.js';
import { toSvg, toDrawio } from './lib/diagram-svg.js';
import { validatePlot, compilePlot, makeView, minTextPt } from './lib/plot-spec.js';
import { csvToValues } from './lib/csv.js';
import { THEME, OKABE_ITO } from './lib/plot-theme.js';

const here = dirname(fileURLToPath(import.meta.url));
const example = (name) => readFileSync(join(here, 'examples', name), 'utf8');
const codes = (issues) => issues.map((i) => i.code);
const stubMeasure = (t) => t.length * 10;

// ---- shared error format -------------------------------------------------------------------

test('formatForAi lists failures and warnings in the paste-back format', () => {
    const text = formatForAi('diagram/1', [ok('fine'), fail('E_X', 'nodes[1]', 'rusak'), warn('W_Y', '', 'hati-hati')]);
    assert.match(text, /^Perbaiki hasil sebelumnya\. Kesalahan dari alat ARTi \(diagram\/1\):/);
    assert.match(text, /1\. \[E_X\] nodes\[1\]: rusak/);
    assert.match(text, /2\. \[W_Y\] hati-hati/);
    assert.match(text, /satu blok kode\.$/);
    assert.equal(formatForAi('x', [ok('fine')]), '');
});

// ---- composite -----------------------------------------------------------------------------

test('parseLen matches compose.py for mm, px and bare numbers', () => {
    assert.ok(Math.abs(parseLen('190mm').px - 718.11) < 0.01);
    assert.equal(parseLen('1328px').px, 1328);
    assert.equal(parseLen('500').unit, 'px');
    assert.throws(() => parseLen('wide'), /tidak terbaca/);
});

test('resolveSpec reads metas, defaults and picks the mode from the unit', () => {
    const print = resolveSpec(example('composite-multi-panel.html'));
    assert.equal(print.spec.mode, 'print');
    assert.equal(print.spec.dpi, 600);
    assert.equal(pngSize(print.spec).w, 4488);
    const pixel = resolveSpec(example('composite-graphical-abstract.html'));
    assert.equal(pixel.spec.mode, 'pixel');
    assert.deepEqual([pixel.spec.wPx, pixel.spec.hPx], [1328, 531]);
    assert.equal(pngSize(pixel.spec).w, 1328);
    assert.deepEqual(codes(resolveSpec('<!doctype html><p>x</p>').issues), ['E_SIZE_MISSING']);
    assert.deepEqual(codes(resolveSpec('<meta name="arti:width" content="abc"><meta name="arti:height" content="5mm">').issues), ['E_SIZE_UNREADABLE']);
});

test('both composite examples pass the static check', () => {
    assert.deepEqual(staticCheck(example('composite-multi-panel.html')), []);
    assert.deepEqual(staticCheck(example('composite-graphical-abstract.html')), []);
});

test('staticCheck refuses scripts, handlers and anything loaded from outside', () => {
    assert.deepEqual(codes(staticCheck('<script>alert(1)</script>')), ['E_SCRIPT']);
    assert.ok(codes(staticCheck('<svg onload="x()"></svg>')).includes('E_HANDLER'));
    assert.ok(codes(staticCheck('<img src="PANEL_A.png">')).includes('E_EXTERNAL'));
    assert.ok(codes(staticCheck('<div style="background:url(https://x.test/a.png)"></div>')).includes('E_EXTERNAL'));
    assert.ok(codes(staticCheck('<style>@import "a.css";</style>')).includes('E_EXTERNAL'));
    assert.ok(codes(staticCheck('<iframe src="data:text/html,x"></iframe>')).includes('E_TAG'));
    assert.deepEqual(staticCheck('<svg><use xlink:href="#a"/><image href="data:image/png;base64,AAAA"/></svg>'), []);
});

test('injectPage forces the page size', () => {
    const { spec } = resolveSpec(example('composite-multi-panel.html'));
    const out = injectPage(example('composite-multi-panel.html'), spec);
    assert.match(out, /@page\{size:190mm 100mm;margin:0;\}/);
});

// ---- diagram -------------------------------------------------------------------------------

for (const name of ['diagram-studi.json', 'diagram-prisma.json', 'diagram-metode.json']) {
    test(`diagram example ${name} lays out without overlap and exports`, () => {
        const { spec, issues } = validateDiagram(example(name));
        assert.ok(spec, JSON.stringify(issues));
        const layout = layoutDiagram(spec, stubMeasure);
        assert.equal(layout.nodes.length, spec.nodes.length);
        for (let i = 0; i < layout.nodes.length; i++) {
            for (let j = i + 1; j < layout.nodes.length; j++) {
                const a = layout.nodes[i], b = layout.nodes[j];
                const apart = a.x + a.w <= b.x || b.x + b.w <= a.x || a.y + a.h <= b.y || b.y + b.h <= a.y;
                assert.ok(apart, `${a.id} overlaps ${b.id}`);
            }
        }
        for (const n of layout.nodes) assert.ok(n.x >= 0 && n.y >= 0 && n.x + n.w <= layout.width && n.y + n.h <= layout.height, n.id);
        const svg = toSvg(layout);
        assert.equal((svg.match(/<g><rect/g) || []).length, spec.nodes.length);
        assert.equal((svg.match(/marker-end/g) || []).length, spec.edges.length);
        const xml = toDrawio(layout);
        assert.equal((xml.match(/vertex="1"/g) || []).length, spec.nodes.length);
        assert.equal((xml.match(/edge="1"/g) || []).length, spec.edges.length);
        assert.ok(xml.includes('source="n_') && xml.startsWith('<mxfile'));
    });
}

test('diagram validation reports each problem with its stable code', () => {
    const base = { arti: 'diagram/1', nodes: [{ id: 'a', label: 'A' }, { id: 'b', label: 'B' }], edges: [{ from: 'a', to: 'b' }] };
    assert.ok(validateDiagram(JSON.stringify(base)).spec);
    const run = (patch) => codes(validateDiagram(JSON.stringify({ ...base, ...patch })).issues);
    assert.ok(run({ arti: 'diagram/2' }).includes('E_MARKER'));
    assert.ok(run({ direction: 'RL' }).includes('E_DIRECTION'));
    assert.ok(run({ nodes: [] }).includes('E_NODES'));
    assert.ok(run({ nodes: [{ id: 'A b', label: 'x' }] }).includes('E_NODE_ID'));
    assert.ok(run({ nodes: [{ id: 'a', label: 'x' }, { id: 'a', label: 'y' }] }).includes('E_NODE_DUP'));
    assert.ok(run({ nodes: [{ id: 'a', label: 'x'.repeat(121) }] }).includes('E_LABEL_LONG'));
    assert.ok(run({ nodes: [{ id: 'a', label: 'x', style: 'neon' }] }).includes('E_STYLE'));
    assert.ok(run({ edges: [{ from: 'a', to: 'zz' }] }).includes('E_EDGE_UNKNOWN_NODE'));
    assert.ok(run({ extra: 1 }).includes('W_UNKNOWN_KEY'));
    assert.ok(run({ nodes: [{ id: 'a', label: 'A' }, { id: 'b', label: 'B' }, { id: 'c', label: 'C' }] }).includes('W_ORPHAN'));
    const many = Array.from({ length: 61 }, (_, i) => ({ id: 'n' + i, label: 'x' }));
    assert.ok(run({ nodes: many, edges: [] }).includes('E_NODES_TOO_MANY'));
    const broken = validateDiagram('{ "arti": "diagram/1",\n "nodes": [ }');
    assert.deepEqual(codes(broken.issues), ['E_JSON']);
    assert.match(broken.issues[0].message, /baris 2/);
});

test('diagram input may arrive inside a Markdown fence', () => {
    const fenced = '```json\n' + example('diagram-metode.json') + '\n```';
    assert.equal(stripFence(fenced), example('diagram-metode.json').trim());
    assert.ok(validateDiagram(fenced).spec);
});

test('wrapText breaks at the max width and keeps over-long words whole', () => {
    assert.deepEqual(wrapText('aaa bbb ccc', stubMeasure, 70), ['aaa bbb', 'ccc']);
    assert.deepEqual(wrapText('supercalifragilistic x', stubMeasure, 70), ['supercalifragilistic', 'x']);
});

test('styles keep dark and plain from figure-style', () => {
    assert.equal(STYLES.dark.fill, '#4D4D4D');
    assert.equal(STYLES.plain.fill, '#FFFFFF');
});

// ---- plot ----------------------------------------------------------------------------------

for (const name of ['plot-batang.json', 'plot-kelompok.json', 'plot-garis.json', 'plot-sebar.json']) {
    test(`plot example ${name} compiles and renders to SVG`, async () => {
        const { spec, issues } = validatePlot(example(name));
        assert.ok(spec, JSON.stringify(issues));
        const { vegaSpec, issues: more } = compilePlot(spec);
        assert.ok(vegaSpec, JSON.stringify(more));
        const view = makeView(vegaSpec, { renderer: 'none' });
        await view.runAsync();
        const svg = await view.toSVG();
        assert.match(svg, /^<svg/);
        assert.ok(minTextPt(svg, 170) > 7, 'text floor at 170 mm: ' + minTextPt(svg, 170));
    });
}

test('plot validation: schema rewrite, missing schema, wrong dialect, data urls', () => {
    const base = { data: { values: [{ a: 1 }] }, mark: 'point', encoding: { x: { field: 'a', type: 'quantitative' } } };
    const v5 = validatePlot(JSON.stringify({ $schema: 'https://vega.github.io/schema/vega-lite/v5.json', ...base }));
    assert.ok(v5.spec && v5.spec.$schema.endsWith('/v6.json'));
    assert.deepEqual(codes(v5.issues), ['W_SCHEMA_VERSION']);
    assert.deepEqual(codes(validatePlot(JSON.stringify(base)).issues), ['W_SCHEMA_MISSING']);
    assert.deepEqual(codes(validatePlot(JSON.stringify({ $schema: 'https://vega.github.io/schema/vega/v5.json', ...base })).issues), ['E_SCHEMA']);
    const url = validatePlot(JSON.stringify({ ...base, data: { url: 'https://example.com/a.csv' } }));
    assert.equal(url.spec, null);
    assert.ok(codes(url.issues).includes('E_DATA_URL'));
    assert.equal(url.issues.find((i) => i.code === 'E_DATA_URL').where, 'data.url');
    assert.deepEqual(codes(validatePlot('{ nope').issues), ['E_JSON']);
});

test('compilePlot turns a Vega-Lite error into E_COMPILE', () => {
    const bad = { $schema: 'x', data: { values: [{ a: 1 }] }, mark: 'point', encoding: { x: { field: 'a', type: 'nonsense' } } };
    const { vegaSpec, issues } = compilePlot(bad);
    assert.equal(vegaSpec, null);
    assert.equal(issues[0].code, 'E_COMPILE');
});

test('the theme is a base: the spec config wins', () => {
    const spec = { data: { values: [{ a: 1, b: 2 }] }, mark: 'bar', config: { background: '#eeeeee' }, encoding: { x: { field: 'a', type: 'ordinal' }, y: { field: 'b', type: 'quantitative' } } };
    assert.equal(compilePlot(spec).vegaSpec.background, '#eeeeee');
    assert.equal(THEME.range.category, OKABE_ITO);
    assert.equal(OKABE_ITO.length, 8);
});

test('csvToValues handles quotes, semicolons with comma decimals, and bad headers', () => {
    assert.deepEqual(csvToValues('tahun,jumlah\n2021,12\n2022,19').values, [{ tahun: 2021, jumlah: 12 }, { tahun: 2022, jumlah: 19 }]);
    assert.deepEqual(csvToValues('nama;skor\n"Budi; A";3,5').values, [{ nama: 'Budi; A', skor: 3.5 }]);
    assert.deepEqual(csvToValues('a\tb\n1\tx').values, [{ a: 1, b: 'x' }]);
    assert.match(csvToValues('a,\n1,2').error, /kosong/);
    assert.match(csvToValues('a,b').error, /minimal satu baris/);
});
