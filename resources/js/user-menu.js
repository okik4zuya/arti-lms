// Avatar dropdown in the top bar (disclosure pattern: button with aria-expanded, panel shown or hidden).
document.querySelectorAll('[data-user-menu]').forEach((menu) => {
    const button = menu.querySelector('.user-menu__button');
    const panel = menu.querySelector('.user-menu__panel');

    if (!button || !panel) {
        return;
    }

    const setOpen = (open) => {
        panel.hidden = !open;
        button.setAttribute('aria-expanded', open ? 'true' : 'false');
    };

    button.addEventListener('click', () => setOpen(panel.hidden));

    document.addEventListener('click', (event) => {
        if (!panel.hidden && !menu.contains(event.target)) {
            setOpen(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !panel.hidden) {
            setOpen(false);
            button.focus();
        }
    });
});
