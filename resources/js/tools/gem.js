import { copyText } from './core/clipboard.js';

// "Salin ..." buttons next to a textarea, and the character counter of the Gem instruction.
document.querySelectorAll('[data-gem-copy]').forEach((btn) => {
    btn.addEventListener('click', async () => {
        const src = document.querySelector(btn.dataset.gemCopy);
        if (!src) return;
        const done = await copyText(src.value ?? src.textContent);
        const status = document.querySelector('[data-gem-status]');
        if (status && btn.closest('.gem')) status.textContent = done ? 'Tersalin.' : 'Gagal menyalin; pilih teksnya lalu salin manual.';
        const old = btn.textContent;
        if (done) {
            btn.textContent = 'Tersalin';
            setTimeout(() => (btn.textContent = old), 1500);
        }
    });
});

const source = document.querySelector('[data-gem-source]');
const count = document.querySelector('[data-gem-count]');
if (source && count) count.textContent = '(' + source.value.length.toLocaleString('id-ID') + ' karakter)';
