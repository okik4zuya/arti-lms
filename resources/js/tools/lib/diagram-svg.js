// SVG and draw.io (.drawio) output of a diagram layout. Strings only, no DOM, so Node tests can check them.
import { STYLES } from './diagram-spec.js';

export const esc = (s) => String(s)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

const n1 = (n) => (Number.isInteger(n) ? String(n) : n.toFixed(1));

export function toSvg(layout) {
    const { width, height, fontFamily, fontSize, lineHeight, strokeWidth } = layout;
    const lineH = fontSize * lineHeight;
    const out = [];
    out.push(`<svg xmlns="http://www.w3.org/2000/svg" width="${width}" height="${height}" viewBox="0 0 ${width} ${height}" font-family="${esc(fontFamily)}" font-size="${fontSize}">`);
    out.push('<defs><marker id="arrow" markerWidth="10" markerHeight="10" refX="8" refY="5" orient="auto" markerUnits="userSpaceOnUse"><path d="M0,0 L10,5 L0,10 z" fill="#000"/></marker></defs>');
    out.push(`<rect width="${width}" height="${height}" fill="#ffffff"/>`);
    for (const e of layout.edges) {
        const d = e.points.map((p, i) => (i ? 'L' : 'M') + n1(p.x) + ',' + n1(p.y)).join(' ');
        out.push(`<path d="${d}" fill="none" stroke="#000" stroke-width="${strokeWidth}" marker-end="url(#arrow)"/>`);
    }
    for (const e of layout.edges) {
        if (!e.label) continue;
        out.push(`<rect x="${n1(e.lx - e.lw / 2)}" y="${n1(e.ly - e.lh / 2)}" width="${e.lw}" height="${e.lh}" fill="#ffffff"/>`);
        out.push(`<text x="${n1(e.lx)}" y="${n1(e.ly)}" text-anchor="middle" dominant-baseline="central">${esc(e.label)}</text>`);
    }
    for (const n of layout.nodes) {
        const st = STYLES[n.style];
        out.push(`<g><rect x="${n1(n.x)}" y="${n1(n.y)}" width="${n.w}" height="${n.h}" fill="${st.fill}" stroke="${st.stroke}" stroke-width="${strokeWidth}"/>`);
        const top = n.y + n.h / 2 - (n.lines.length * lineH) / 2 + lineH / 2;
        n.lines.forEach((line, i) => {
            out.push(`<text x="${n1(n.x + n.w / 2)}" y="${n1(top + i * lineH)}" text-anchor="middle" dominant-baseline="central" fill="${st.text}">${esc(line)}</text>`);
        });
        out.push('</g>');
    }
    out.push('</svg>');
    return out.join('\n');
}

/** draw.io file with the same cell styles as tools/arti-render/render.py. Text wrapping is left to draw.io. */
export function toDrawio(layout, name = 'diagram') {
    const { fontSize, strokeWidth, padX, direction } = layout;
    const exit = direction === 'LR' ? 'exitX=1;exitY=0.5;entryX=0;entryY=0.5' : 'exitX=0.5;exitY=1;entryX=0.5;entryY=0';
    const L = [];
    L.push('<mxfile host="arti-tools" version="28.0.6">');
    L.push(`  <diagram name="${esc(name)}" id="arti-${esc(name)}">`);
    L.push(`    <mxGraphModel dx="2182" dy="1158" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="${layout.width}" pageHeight="${layout.height}" math="0" shadow="0">`);
    L.push('      <root>');
    L.push('        <mxCell id="0" />');
    L.push('        <mxCell id="1" parent="0" />');
    layout.edges.forEach((e, i) => {
        const style = `edgeStyle=orthogonalEdgeStyle;rounded=0;orthogonalLoop=1;jettySize=auto;html=1;${exit};strokeWidth=${strokeWidth};fontSize=${fontSize};`;
        L.push(`        <mxCell id="e${i + 1}" value="${esc(e.label || '')}" style="${style}" edge="1" parent="1" source="n_${e.from}" target="n_${e.to}">`);
        L.push('          <mxGeometry relative="1" as="geometry" />');
        L.push('        </mxCell>');
    });
    for (const n of layout.nodes) {
        const st = STYLES[n.style];
        const style = `text;html=1;align=center;verticalAlign=middle;whiteSpace=wrap;rounded=0;strokeColor=${st.stroke};strokeWidth=${strokeWidth};fontSize=${fontSize};spacingLeft=${padX};spacingRight=${padX};fillColor=${st.fill};fontColor=${st.text};`;
        L.push(`        <mxCell id="n_${n.id}" value="${esc(n.lines.join(' '))}" style="${style}" vertex="1" parent="1">`);
        L.push(`          <mxGeometry x="${Math.round(n.x)}" y="${Math.round(n.y)}" width="${n.w}" height="${n.h}" as="geometry" />`);
        L.push('        </mxCell>');
    }
    L.push('      </root>', '    </mxGraphModel>', '  </diagram>', '</mxfile>');
    return L.join('\r\n') + '\r\n';
}
