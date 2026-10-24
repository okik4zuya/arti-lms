// CSV/TSV pasted from a spreadsheet -> rows for Vega-Lite `data.values`. No library; handles quotes,
// comma / semicolon / tab delimiters, and comma decimals when the delimiter is not a comma.

export function detectDelimiter(firstLine) {
    const counts = { '\t': 0, ';': 0, ',': 0 };
    let quoted = false;
    for (const ch of firstLine) {
        if (ch === '"') quoted = !quoted;
        else if (!quoted && ch in counts) counts[ch] += 1;
    }
    const [best, n] = Object.entries(counts).sort((a, b) => b[1] - a[1])[0];
    return n ? best : ',';
}

export function parseRows(text, delimiter) {
    const rows = [];
    let row = [], cell = '', quoted = false;
    const s = text.replace(/\r\n?/g, '\n');
    for (let i = 0; i < s.length; i++) {
        const ch = s[i];
        if (quoted) {
            if (ch === '"' && s[i + 1] === '"') { cell += '"'; i++; }
            else if (ch === '"') quoted = false;
            else cell += ch;
        } else if (ch === '"') quoted = true;
        else if (ch === delimiter) { row.push(cell); cell = ''; }
        else if (ch === '\n') { row.push(cell); rows.push(row); row = []; cell = ''; }
        else cell += ch;
    }
    if (cell !== '' || row.length) { row.push(cell); rows.push(row); }
    return rows.filter((r) => r.some((c) => c.trim() !== ''));
}

function toValue(raw, delimiter) {
    const t = raw.trim();
    if (t === '') return null;
    const n = delimiter === ',' ? t : t.replace(',', '.');
    if (/^[-+]?(\d+\.?\d*|\.\d+)(e[-+]?\d+)?$/i.test(n)) return Number(n);
    return t;
}

/** Returns { values, error }. First row is the header; empty header cells are an error. */
export function csvToValues(text) {
    const src = text.trim();
    if (!src) return { values: [], error: 'tempel data dulu.' };
    const delimiter = detectDelimiter(src.split('\n')[0]);
    const rows = parseRows(src, delimiter);
    if (rows.length < 2) return { values: [], error: 'butuh baris judul kolom dan minimal satu baris data.' };
    const head = rows[0].map((h) => h.trim());
    if (head.some((h) => !h)) return { values: [], error: 'ada judul kolom yang kosong.' };
    const values = rows.slice(1).map((r) => Object.fromEntries(head.map((h, i) => [h, toValue(r[i] ?? '', delimiter)])));
    return { values, error: null };
}
