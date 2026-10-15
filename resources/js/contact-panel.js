// WhatsApp contact panel. The floating button, every link to /kontak and every [data-kontak] button
// open one <dialog> holding the contact form. All of them keep a real href (wa.me or /kontak), so
// without this script they still work; this only upgrades them. Nothing is sent to the server: the
// message is built here and opened on wa.me. Keep the format identical to ContactController::send().

const panel = document.getElementById('wa-panel');

if (panel) {
    const float = document.querySelector('.wa-float');
    const form = panel.querySelector('[data-contact-form]');
    const nama = form.elements.nama;
    const topik = form.elements.topik;
    const pesan = form.elements.pesan;
    const pesanError = form.querySelector('[data-error-for="pesan"]');
    const wa = panel.dataset.wa;
    const templates = JSON.parse(panel.dataset.templates || '{}');
    const mobile = window.matchMedia('(max-width: 767px)');
    const DRAFT = 'arti-wa-draft';
    const SEEN = 'arti-wa-seen';
    let trigger = null;

    const store = {
        get(key) {
            try { return JSON.parse(sessionStorage.getItem(key) || 'null'); } catch (_) { return null; }
        },
        set(key, value) {
            try { sessionStorage.setItem(key, JSON.stringify(value)); } catch (_) {}
        },
    };

    // A message the visitor has not edited: empty, or one of the topic templates.
    const untouched = () => pesan.value.trim() === '' || Object.values(templates).includes(pesan.value.trim());
    const applyTemplate = () => { if (untouched()) pesan.value = templates[topik.value] || ''; };
    const saveDraft = () => store.set(DRAFT, { nama: nama.value, topik: topik.value, pesan: pesan.value });

    const draft = store.get(DRAFT);
    if (draft) {
        nama.value = draft.nama || '';
        if ([...topik.options].some((o) => o.value === draft.topik)) topik.value = draft.topik;
        pesan.value = draft.pesan || '';
    }
    if (store.get(SEEN)) float?.classList.add('is-seen');

    const clearError = () => {
        pesanError.hidden = true;
        pesan.removeAttribute('aria-invalid');
        pesan.removeAttribute('aria-describedby');
    };

    const open = (el, topic) => {
        trigger = el;
        if (topic && [...topik.options].some((o) => o.value === topic)) topik.value = topic;
        applyTemplate();
        saveDraft();
        if (!panel.open) {
            mobile.matches ? panel.showModal() : panel.show();
            if (location.hash !== '#kontak') history.pushState({ waPanel: true }, '', '#kontak');
        }
        float?.setAttribute('aria-expanded', 'true');
        float?.classList.add('is-seen');
        store.set(SEEN, true);
        // On a phone the keyboard would cover the sheet, so only focus a field on larger screens.
        if (!mobile.matches) (nama.value ? pesan : nama).focus();
    };

    // The 'close' event covers every way out: the – button, Esc, backdrop, Back and a finished send.
    panel.addEventListener('close', () => {
        float?.setAttribute('aria-expanded', 'false');
        clearError();
        if (history.state && history.state.waPanel) history.back();
        else if (location.hash === '#kontak') history.replaceState(null, '', location.pathname + location.search);
        (trigger && trigger.isConnected ? trigger : float)?.focus();
        trigger = null;
    });
    window.addEventListener('popstate', () => { if (panel.open) panel.close(); });
    // A link to #kontak on the page we are already on changes only the hash, so nothing reloads.
    window.addEventListener('hashchange', () => { if (location.hash === '#kontak' && !panel.open) open(null, null); });
    panel.addEventListener('keydown', (e) => { if (e.key === 'Escape' && panel.open) panel.close(); });
    panel.querySelector('[data-wa-close]').addEventListener('click', () => panel.close());
    // A click on the modal backdrop lands on the <dialog> itself, outside its box.
    panel.addEventListener('click', (e) => {
        if (e.target !== panel) return;
        const r = panel.getBoundingClientRect();
        if (e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom) panel.close();
    });

    document.addEventListener('click', (e) => {
        if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        const target = e.target.closest('.wa-float, [data-kontak], a[href*="kontak"]');
        if (!target) return;

        let topic = target.dataset.kontak || null;
        if (target.matches('a[href*="kontak"]') && !target.matches('.wa-float, [data-kontak]')) {
            const url = new URL(target.href, location.href);
            if (url.origin !== location.origin || url.pathname.replace(/\/$/, '') !== '/kontak') return;
            topic = url.searchParams.get('topik');
        }

        e.preventDefault();
        if (target === float && panel.open) panel.close();
        else open(target, topic);
    });

    topik.addEventListener('change', () => { applyTemplate(); saveDraft(); });
    [nama, pesan].forEach((f) => f.addEventListener('input', () => { if (f === pesan) clearError(); saveDraft(); }));

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const text = pesan.value.trim();
        if (!text) {
            pesanError.textContent = 'Tulis pesan Anda.';
            pesanError.id = 'wa-pesan-js-error';
            pesanError.hidden = false;
            pesan.setAttribute('aria-invalid', 'true');
            pesan.setAttribute('aria-describedby', pesanError.id);
            pesan.focus();
            return;
        }
        const name = nama.value.trim();
        const label = topik.options[topik.selectedIndex].text;
        const message = `${name ? `Halo, saya ${name}.` : 'Halo.'}\nKeperluan: ${label}\n\n${text}`;

        // A clicked anchor inside the submit handler keeps the user gesture, so popup blockers allow it.
        const link = document.createElement('a');
        link.href = `${wa}?text=${encodeURIComponent(message)}`;
        link.target = '_blank';
        link.rel = 'noopener';
        document.body.appendChild(link);
        link.click();
        link.remove();

        pesan.value = '';
        saveDraft();
        panel.close();
    });

    if (location.hash === '#kontak') open(null, null);
}
