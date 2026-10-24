// diagram/1: nodes and edges with no coordinates. Pure functions, unit-tested with `node --test`.
// Spec (also the text of the Gem knowledge file):
//   { "arti": "diagram/1", "direction": "TB"|"LR",
//     "nodes": [ { "id": "a", "label": "text", "style": "dark"|"plain"|"soft" } ],
//     "edges": [ { "from": "a", "to": "b", "label": "optional" } ] }
import { fail, warn } from '../core/errors.js';

export const CONTRACT = 'diagram/1';
export const MAX_NODES = 60;
export const MAX_LABEL = 120;
export const MAX_EDGE_LABEL = 40;

// dark and plain come from figure-style-default.md; soft is new for the free tool.
export const STYLES = {
    dark: { fill: '#4D4D4D', stroke: '#000000', text: '#FFFFFF' },
    plain: { fill: '#FFFFFF', stroke: '#000000', text: '#000000' },
    soft: { fill: '#F2F2F2', stroke: '#4D4D4D', text: '#000000' },
};
export const DEFAULTS = { fontFamily: 'Arial, Helvetica, sans-serif', fontSize: 20, lineHeight: 1.2, strokeWidth: 2, padX: 20 };

const ID = /^[a-z0-9_]+$/;
const KNOWN = ['arti', 'direction', 'nodes', 'edges'];

/** Drop a Markdown fence if the whole answer was pasted with it. */
export function stripFence(text) {
    const m = /^\s*```[a-z]*\s*\n([\s\S]*?)\n?```\s*$/i.exec(text);
    return (m ? m[1] : text).trim();
}

/** JSON.parse with a message that says where (line, column) when the engine lets us find out. */
export function parseJson(text) {
    try {
        return { value: JSON.parse(text) };
    } catch (e) {
        let pos = -1;
        const byNumber = /position (\d+)/.exec(e.message);
        if (byNumber) {
            pos = +byNumber[1];
        } else {
            // newer V8 quotes the text up to the error instead: Unexpected token 'x', ..."context" is not valid JSON
            const ctx = /, (?:\.\.\.)?"([\s\S]*)" is not valid JSON/.exec(e.message);
            const at = ctx && ctx[1] ? text.lastIndexOf(ctx[1]) : -1;
            if (at >= 0) pos = at + ctx[1].length - 1;
        }
        let where = '';
        if (pos >= 0) {
            const upto = text.slice(0, pos);
            where = ` (baris ${upto.split('\n').length}, kolom ${pos - upto.lastIndexOf('\n')})`;
        }
        const reason = e.message.replace(/ in JSON at position \d+.*$/, '').replace(/, (?:\.\.\.)?"[\s\S]*" is not valid JSON$/, '');
        return { error: `JSON tidak valid${where}: ${reason}.` };
    }
}

/** Returns { spec, issues }. spec is null when the source cannot be drawn. */
export function validateDiagram(text) {
    const issues = [];
    const parsed = parseJson(stripFence(text));
    if (parsed.error) return { spec: null, issues: [fail('E_JSON', 'JSON', parsed.error)] };
    const s = parsed.value;
    if (!s || typeof s !== 'object' || Array.isArray(s)) {
        return { spec: null, issues: [fail('E_JSON', 'JSON', 'isi harus berupa satu objek { ... }.')] };
    }
    if (s.arti !== CONTRACT) {
        issues.push(fail('E_MARKER', 'arti', `harus "arti": "${CONTRACT}" (ditemukan ${JSON.stringify(s.arti)}).`));
    }
    for (const k of Object.keys(s)) {
        if (!KNOWN.includes(k)) issues.push(warn('W_UNKNOWN_KEY', k, 'kunci ini tidak dikenal dan diabaikan.'));
    }
    const direction = s.direction === undefined ? 'TB' : s.direction;
    if (direction !== 'TB' && direction !== 'LR') {
        issues.push(fail('E_DIRECTION', 'direction', `harus "TB" atau "LR" (ditemukan ${JSON.stringify(s.direction)}).`));
    }

    const nodes = Array.isArray(s.nodes) ? s.nodes : null;
    if (!nodes || !nodes.length) {
        issues.push(fail('E_NODES', 'nodes', 'harus berupa daftar berisi minimal satu node.'));
        return { spec: null, issues };
    }
    if (nodes.length > MAX_NODES) {
        issues.push(fail('E_NODES_TOO_MANY', 'nodes', `${nodes.length} node, maksimal ${MAX_NODES}. Pecah menjadi dua diagram.`));
    }
    const seen = new Set();
    nodes.forEach((n, i) => {
        const at = `nodes[${i}]`;
        if (!n || typeof n !== 'object') {
            issues.push(fail('E_NODE', at, 'harus berupa objek { id, label, style }.'));
            return;
        }
        if (typeof n.id !== 'string' || !ID.test(n.id)) {
            issues.push(fail('E_NODE_ID', at + '.id', `id harus huruf kecil, angka, atau _ (ditemukan ${JSON.stringify(n.id)}).`));
        } else if (seen.has(n.id)) {
            issues.push(fail('E_NODE_DUP', at + '.id', `id "${n.id}" dipakai dua kali.`));
        } else {
            seen.add(n.id);
        }
        if (typeof n.label !== 'string' || !n.label.trim()) {
            issues.push(fail('E_LABEL_MISSING', at + '.label', 'label teks wajib ada.'));
        } else if (n.label.length > MAX_LABEL) {
            issues.push(fail('E_LABEL_LONG', at + '.label', `${n.label.length} karakter, maksimal ${MAX_LABEL}. Persingkat.`));
        }
        if (n.style !== undefined && !STYLES[n.style]) {
            issues.push(fail('E_STYLE', at + '.style', `"${n.style}" tidak dikenal; pilih dark, plain, atau soft.`));
        }
    });

    const edges = s.edges === undefined ? [] : s.edges;
    if (!Array.isArray(edges)) {
        issues.push(fail('E_EDGES', 'edges', 'harus berupa daftar { from, to }.'));
        return { spec: null, issues };
    }
    const touched = new Set();
    edges.forEach((e, i) => {
        const at = `edges[${i}]`;
        if (!e || typeof e !== 'object') {
            issues.push(fail('E_EDGE', at, 'harus berupa objek { from, to }.'));
            return;
        }
        for (const end of ['from', 'to']) {
            if (!seen.has(e[end])) {
                issues.push(fail('E_EDGE_UNKNOWN_NODE', `${at}.${end}`, `${end} = ${JSON.stringify(e[end])} tidak ada di nodes.`));
            } else {
                touched.add(e[end]);
            }
        }
        if (e.from !== undefined && e.from === e.to) {
            issues.push(warn('W_EDGE_SELF', at, 'panah dari node ke dirinya sendiri tidak digambar.'));
        }
        if (e.label !== undefined) {
            if (typeof e.label !== 'string') issues.push(fail('E_EDGE_LABEL', at + '.label', 'harus berupa teks.'));
            else if (e.label.length > MAX_EDGE_LABEL) issues.push(warn('W_EDGE_LABEL_LONG', at + '.label', `${e.label.length} karakter; label panah sebaiknya di bawah ${MAX_EDGE_LABEL}.`));
        }
    });
    if (nodes.length > 1) {
        for (const id of seen) {
            if (!touched.has(id)) issues.push(warn('W_ORPHAN', `node "${id}"`, 'tidak terhubung ke panah mana pun.'));
        }
    }

    if (issues.some((i) => i.level === 'fail')) return { spec: null, issues };
    return {
        spec: {
            direction,
            nodes: nodes.map((n) => ({ id: n.id, label: n.label.trim(), style: n.style || 'plain' })),
            edges: edges.filter((e) => e.from !== e.to).map((e) => ({ from: e.from, to: e.to, label: (e.label || '').trim() })),
        },
        issues,
    };
}
