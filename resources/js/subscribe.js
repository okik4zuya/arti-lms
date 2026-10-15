// Mailing-list forms. Every form works as a plain POST without this script; this only sends it with
// fetch so the page stays where it is, and remembers a finished or dismissed card for this browser.

const KEY = 'arti-subscribe';
const store = {
    get() { try { return localStorage.getItem(KEY); } catch (_) { return null; } },
    set(value) { try { localStorage.setItem(KEY, value); } catch (_) {} },
};

// "Tidak sekarang" only silences the cards that have that button (the send-me-the-link offer on pages 1-3).
// The closing offer on the last page has none, so it still shows to someone who dismissed earlier;
// only a finished sign-up hides every card.
const cards = document.querySelectorAll('[data-subscribe-card]');
const state = store.get();
cards.forEach((card) => {
    const dismiss = card.querySelector('[data-subscribe-dismiss]');
    if (state === 'subscribed' || (state === 'dismissed' && dismiss)) card.hidden = true;
    dismiss?.addEventListener('click', () => {
        if (store.get() !== 'subscribed') store.set('dismissed');
        card.hidden = true;
    });
});

document.querySelectorAll('form[data-subscribe]').forEach((form) => {
    const email = form.elements.email;
    const error = form.querySelector('[data-subscribe-error]');
    const status = form.querySelector('[data-subscribe-status]');
    const button = form.querySelector('[data-subscribe-button]');

    const showError = (text) => {
        error.textContent = text;
        error.hidden = false;
        email.setAttribute('aria-invalid', 'true');
        email.setAttribute('aria-describedby', error.id);
        email.focus();
    };

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        error.hidden = true;
        email.removeAttribute('aria-invalid');

        if (!email.value.trim() || !email.checkValidity()) {
            showError(email.value.trim() ? 'Alamat email tampaknya belum benar.' : 'Tulis alamat email Anda.');
            return;
        }

        button.disabled = true;
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form),
            });
            const data = await response.json().catch(() => ({}));

            if (response.status === 422) {
                showError(data.errors?.email?.[0] || 'Alamat email tampaknya belum benar.');
            } else if (response.ok) {
                status.textContent = data.message;
                status.hidden = false;
                button.hidden = true;
                form.querySelector('[data-subscribe-fields]').hidden = true;
                store.set('subscribed');
            } else {
                showError('Belum berhasil terkirim. Coba lagi sebentar lagi.');
            }
        } catch (_) {
            showError('Belum berhasil terkirim. Periksa koneksi Anda lalu coba lagi.');
        } finally {
            button.disabled = false;
        }
    });
});

// ARTi PDF: the download button is a real link to /artipdf/unduh. The click is left alone so the
// download starts; the dialog only adds the thank-you and the offer. Shown once per session.
const dialog = document.getElementById('download-thanks');
if (dialog) {
    const SEEN = 'arti-download-thanks';
    const seen = () => { try { return sessionStorage.getItem(SEEN); } catch (_) { return null; } };
    let trigger = null;

    document.querySelectorAll('[data-download]').forEach((link) => {
        link.addEventListener('click', (e) => {
            if (e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
            if (seen() || store.get() === 'subscribed') return;
            trigger = link;
            try { sessionStorage.setItem(SEEN, '1'); } catch (_) {}
            if (!dialog.open) dialog.showModal();
        });
    });

    dialog.addEventListener('close', () => { trigger?.focus(); trigger = null; });
    dialog.querySelector('[data-dialog-close]')?.addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', (e) => {
        if (e.target !== dialog) return;
        const r = dialog.getBoundingClientRect();
        if (e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom) dialog.close();
    });
}
