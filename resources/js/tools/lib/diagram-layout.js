// Auto-layout for diagram/1 with dagre. `measure(text)` returns the pixel width of one line of text, so the
// layout is testable in Node with a stub and uses canvas measureText in the browser.
import dagre from '@dagrejs/dagre';
import { DEFAULTS } from './diagram-spec.js';

export const MAX_TEXT_W = 260; // wrap labels at this width, like draw.io wraps inside a box
export const MIN_NODE_W = 120;
const PAD_Y = 14;
const EDGE_LABEL_PAD = 8;

/** Greedy word wrap. A word wider than maxW keeps its own line. */
export function wrapText(text, measure, maxW = MAX_TEXT_W) {
    const lines = [];
    for (const para of text.split(/\n/)) {
        let line = '';
        for (const word of para.split(/\s+/).filter(Boolean)) {
            const trial = line ? line + ' ' + word : word;
            if (line && measure(trial) > maxW) {
                lines.push(line);
                line = word;
            } else {
                line = trial;
            }
        }
        lines.push(line);
    }
    return lines;
}

export function layoutDiagram(spec, measure, opts = {}) {
    const d = { ...DEFAULTS, ...opts };
    const lineH = d.fontSize * d.lineHeight;
    const g = new dagre.graphlib.Graph({ multigraph: true });
    g.setGraph({
        rankdir: spec.direction,
        nodesep: 40,
        ranksep: 64,
        marginx: 20,
        marginy: 20,
    });
    g.setDefaultEdgeLabel(() => ({}));

    const info = new Map();
    for (const n of spec.nodes) {
        const lines = wrapText(n.label, measure);
        const textW = Math.max(...lines.map((l) => measure(l)));
        const w = Math.max(MIN_NODE_W, Math.ceil(textW + 2 * d.padX));
        const h = Math.ceil(lines.length * lineH + 2 * PAD_Y);
        info.set(n.id, { ...n, lines, w, h });
        g.setNode(n.id, { width: w, height: h });
    }
    spec.edges.forEach((e, i) => {
        const label = {};
        if (e.label) {
            label.width = Math.ceil(measure(e.label) + 2 * EDGE_LABEL_PAD);
            label.height = Math.ceil(lineH + 4);
            label.labelpos = 'c';
        }
        g.setEdge(e.from, e.to, label, 'e' + i);
    });
    dagre.layout(g);

    const nodes = spec.nodes.map((n) => {
        const p = g.node(n.id);
        const i = info.get(n.id);
        return { id: n.id, style: n.style, lines: i.lines, w: i.w, h: i.h, x: p.x - i.w / 2, y: p.y - i.h / 2 };
    });
    const edges = spec.edges.map((e, i) => {
        const p = g.edge(e.from, e.to, 'e' + i);
        return { from: e.from, to: e.to, label: e.label, points: p.points, lx: p.x, ly: p.y, lw: p.width || 0, lh: p.height || 0 };
    });
    const graph = g.graph();
    return { nodes, edges, width: Math.ceil(graph.width), height: Math.ceil(graph.height), direction: spec.direction, ...d };
}
