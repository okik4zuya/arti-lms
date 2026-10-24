// One issue format for every tool: a level, a stable code, where it is, and a message for a person.
// `formatForAi` turns the list into text meant to be pasted back into the chat that wrote the source.

export const FAIL = 'fail';
export const WARN = 'warn';
export const OK = 'ok';

export function issue(level, code, where, message) {
    return { level, code, where: where || '', message };
}

export const fail = (code, where, message) => issue(FAIL, code, where, message);
export const warn = (code, where, message) => issue(WARN, code, where, message);
export const ok = (message) => issue(OK, 'OK', '', message);

export const hasFail = (issues) => issues.some((i) => i.level === FAIL);

export function formatForAi(contract, issues) {
    const items = issues.filter((i) => i.level !== OK);
    if (!items.length) return '';
    const lines = items.map((i, n) => `${n + 1}. [${i.code}] ${i.where ? i.where + ': ' : ''}${i.message}`);
    return [
        `Perbaiki hasil sebelumnya. Kesalahan dari alat ARTi (${contract}):`,
        ...lines,
        'Kirim ulang seluruh sumber yang sudah diperbaiki, hanya dalam satu blok kode.',
    ].join('\n');
}

const LABEL = { fail: 'GAGAL', warn: 'PERINGATAN', ok: 'OK' };

export function paintReport(root, issues) {
    const list = root.querySelector('[data-wb-report-list]');
    const fix = root.querySelector('[data-wb-fix]');
    root.hidden = issues.length === 0;
    list.textContent = '';
    for (const i of issues) {
        const li = document.createElement('li');
        li.className = 'is-' + i.level;
        const b = document.createElement('b');
        b.textContent = LABEL[i.level] + (i.code !== 'OK' ? ' ' + i.code : '');
        li.append(b, ' ' + (i.where ? i.where + ': ' : '') + i.message);
        list.append(li);
    }
    fix.hidden = !issues.some((i) => i.level !== OK);
}
