// Checks for Vega-Lite specs written by an AI. Pure functions (no DOM), unit-tested with `node --test`.
import * as vl from 'vega-lite';
import * as vega from 'vega';
import { fail, warn } from '../core/errors.js';
import { stripFence, parseJson } from './diagram-spec.js';
import { THEME } from './plot-theme.js';

export const CONTRACT = 'vega-lite';
export const MAX_ROWS = 5000;
export const BUNDLED_MAJOR = parseInt(String(vl.version).split('.')[0], 10);
export const SCHEMA_URL = `https://vega.github.io/schema/vega-lite/v${BUNDLED_MAJOR}.json`;


/** Walk the spec: every `url` is a network read, every `data.values` array adds rows. */
function scan(node, path, found) {
    if (Array.isArray(node)) {
        node.forEach((v, i) => scan(v, `${path}[${i}]`, found));
    } else if (node && typeof node === 'object') {
        for (const [k, v] of Object.entries(node)) {
            const p = path ? `${path}.${k}` : k;
            if (k === 'url' && typeof v === 'string') found.urls.push(p);
            else if (k === 'values' && Array.isArray(v) && /(^|\.)data$/.test(path)) found.rows += v.length;
            else scan(v, p, found);
        }
    }
}

/** Returns { spec, issues }; spec is ready to compile (schema rewritten to the bundled major). */
export function validatePlot(text) {
    const issues = [];
    const parsed = parseJson(stripFence(text));
    if (parsed.error) return { spec: null, issues: [fail('E_JSON', 'JSON', parsed.error)] };
    const s = parsed.value;
    if (!s || typeof s !== 'object' || Array.isArray(s)) {
        return { spec: null, issues: [fail('E_JSON', 'JSON', 'isi harus berupa satu objek { ... }.')] };
    }

    const schema = s.$schema;
    if (schema === undefined) {
        issues.push(warn('W_SCHEMA_MISSING', '$schema', `tidak ada; dianggap Vega-Lite v${BUNDLED_MAJOR}. Minta AI menambahkan "$schema": "${SCHEMA_URL}".`));
    } else if (typeof schema !== 'string' || !/vega-lite/i.test(schema)) {
        issues.push(fail('E_SCHEMA', '$schema', `harus Vega-Lite (…/schema/vega-lite/v${BUNDLED_MAJOR}.json); ditemukan ${JSON.stringify(schema)}. Minta Vega-Lite, bukan Vega atau format lain.`));
        return { spec: null, issues };
    } else {
        const major = /\/v(\d+)/.exec(schema);
        if (!major || +major[1] !== BUNDLED_MAJOR) {
            issues.push(warn('W_SCHEMA_VERSION', '$schema', `${schema} diubah ke v${BUNDLED_MAJOR}; periksa hasilnya karena beberapa pengaturan berbeda antarversi.`));
        }
    }

    const found = { urls: [], rows: 0 };
    scan(s, '', found);
    for (const p of found.urls) {
        issues.push(fail('E_DATA_URL', p, 'tautan data ke luar tidak diizinkan. Taruh data langsung di "data": { "values": [ ... ] }.'));
    }
    if (found.rows > MAX_ROWS) {
        issues.push(warn('W_ROWS', 'data.values', `${found.rows} baris; di atas ${MAX_ROWS} grafik bisa lambat.`));
    }
    if (issues.some((i) => i.level === 'fail')) return { spec: null, issues };

    return { spec: { ...s, $schema: SCHEMA_URL }, issues };
}

/** A loader that refuses every read: all data must already be inside the spec. */
export const denyLoader = {
    load: () => Promise.reject(new Error('pembacaan data dari luar diblokir')),
    sanitize: () => Promise.reject(new Error('pembacaan data dari luar diblokir')),
    http: () => Promise.reject(new Error('pembacaan data dari luar diblokir')),
    file: () => Promise.reject(new Error('pembacaan data dari luar diblokir')),
};

/** Compile with the house theme under the spec's config. Returns { vegaSpec, issues } or throws a readable error. */
export function compilePlot(spec) {
    const issues = [];
    const messages = [];
    const logger = {
        level: () => 2,
        warn: (...a) => { messages.push(a.map(String).join(' ')); return logger; },
        info: () => logger,
        debug: () => logger,
        error: (...a) => { throw new Error(a.map(String).join(' ')); },
    };
    let out;
    try {
        out = vl.compile(spec, { config: THEME, logger });
    } catch (e) {
        issues.push(fail('E_COMPILE', 'Vega-Lite', String(e.message || e).replace(/^Error:\s*/, '')));
        return { vegaSpec: null, issues };
    }
    for (const m of new Set(messages)) issues.push(warn('W_VEGALITE', 'Vega-Lite', m));
    return { vegaSpec: out.spec, issues };
}

/** Build a headless view from a compiled spec (used for SVG/PNG output and by Node tests). */
export function makeView(vegaSpec, opts = {}) {
    return new vega.View(vega.parse(vegaSpec), { loader: denyLoader, logLevel: vega.Warn, ...opts });
}

/** Smallest text in an SVG string, in points when the chart is printed `widthMm` wide. */
export function minTextPt(svg, widthMm) {
    const w = parseFloat(/<svg[^>]*\swidth="([0-9.]+)"/.exec(svg)?.[1]);
    if (!w) return null;
    const sizes = [...svg.matchAll(/font-size="([0-9.]+)(?:px)?"/g)].map((m) => parseFloat(m[1]));
    if (!sizes.length) return null;
    const mmPerPx = widthMm / w;
    return Math.min(...sizes) * mmPerPx * (72 / 25.4);
}
