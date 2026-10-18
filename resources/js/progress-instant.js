// "Tandai selesai" without a page load: the page shows the subchapter as done at once and saves in the background.
// If saving fails, the button and the sidebar mark come back with a message. Without JavaScript the plain form still works.
document.querySelectorAll('[data-progress-form]').forEach((form) => {
    const slug = form.dataset.slug;
    const row = form.closest('.chapter-complete');
    const sidebarMark = [...document.querySelectorAll('.sidebar .tree-item')]
        .find((link) => new URL(link.href, location.href).pathname.endsWith('/' + slug))
        ?.querySelector('.tree-item__index');

    if (!row || !slug) {
        return;
    }

    const doneNode = () => {
        const done = document.createElement('span');
        const check = document.createElement('span');

        check.className = 'tree-item__check tree-item__check--done';
        check.textContent = '✓';
        done.append(check, ' Selesai');

        return done;
    };

    const setSidebar = (done) => {
        if (!sidebarMark) {
            return;
        }

        if (done) {
            sidebarMark.dataset.before = sidebarMark.textContent;
            sidebarMark.textContent = '✓';
            sidebarMark.classList.add('tree-item__index--done');
        } else {
            sidebarMark.textContent = sidebarMark.dataset.before ?? sidebarMark.textContent;
            sidebarMark.classList.remove('tree-item__index--done');
        }
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        row.querySelector('.form-error')?.remove();

        const done = doneNode();

        form.replaceWith(done);
        setSidebar(true);

        let failure = null;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                body: new FormData(form),
            });

            if (response.status === 401 || response.status === 419) {
                failure = 'Sesi Anda habis. Muat ulang halaman dan masuk lagi.';
            } else if (!response.ok) {
                failure = 'Gagal menyimpan. Coba lagi.';
            }
        } catch (error) {
            failure = 'Tidak ada koneksi. Coba lagi.';
        }

        if (failure === null) {
            return;
        }

        done.replaceWith(form);
        setSidebar(false);

        const message = document.createElement('span');

        message.className = 'form-error';
        message.setAttribute('role', 'alert');
        message.textContent = failure;
        row.append(message);
    });
});
