import './bootstrap';

const sidebar = document.querySelector('.sidebar');
const toggle = document.querySelector('.sidebar-toggle');
const backdrop = document.querySelector('.sidebar-backdrop');

if (sidebar && toggle && backdrop) {
    const open = () => {
        sidebar.classList.add('is-open');
        backdrop.hidden = false;
        toggle.setAttribute('aria-expanded', 'true');
    };
    const close = () => {
        sidebar.classList.remove('is-open');
        backdrop.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
    };

    toggle.addEventListener('click', () => {
        sidebar.classList.contains('is-open') ? close() : open();
    });
    backdrop.addEventListener('click', close);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
    });
}
