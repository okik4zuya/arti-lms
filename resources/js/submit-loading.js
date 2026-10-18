// Shows a spinner on the pressed submit button and ignores a second submit while the page is leaving.
const busy = new Set();

document.addEventListener('submit', (event) => {
    const form = event.target;

    // A form that stays on the page (handled by script, opens a new tab) must not be left spinning.
    if (!(form instanceof HTMLFormElement) || event.defaultPrevented || form.target === '_blank' || form.hasAttribute('data-no-loading')) {
        return;
    }

    if (busy.has(form)) {
        event.preventDefault();

        return;
    }

    const button = event.submitter instanceof HTMLButtonElement ? event.submitter : form.querySelector('button[type="submit"]');

    if (!button) {
        return;
    }

    busy.add(form);
    button.classList.add('is-loading');
    button.setAttribute('aria-busy', 'true');
});

// Back button (page restored from the cache): the buttons must be usable again.
window.addEventListener('pageshow', (event) => {
    if (!event.persisted) {
        return;
    }

    busy.clear();
    document.querySelectorAll('.btn.is-loading').forEach((button) => {
        button.classList.remove('is-loading');
        button.removeAttribute('aria-busy');
    });
});
