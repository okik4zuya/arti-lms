// Show/hide button on password fields, and the live requirement list under a new-password field.
document.querySelectorAll('[data-password-field]').forEach((field) => {
    const input = field.querySelector('input');
    const toggle = field.querySelector('[data-password-toggle]');

    if (!input || !toggle) {
        return;
    }

    toggle.addEventListener('click', () => {
        const show = input.type === 'password';

        input.type = show ? 'text' : 'password';
        toggle.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        toggle.setAttribute('aria-pressed', show ? 'true' : 'false');
    });
});

document.querySelectorAll('[data-password-rules]').forEach((list) => {
    const input = document.getElementById(list.dataset.passwordRules);

    if (!input) {
        return;
    }

    const checks = {
        length: (value) => [...value].length >= 10,
        letter: (value) => /\p{L}/u.test(value),
        number: (value) => /\p{N}/u.test(value),
    };

    const update = () => {
        list.querySelectorAll('[data-rule]').forEach((item) => {
            item.classList.toggle('is-met', checks[item.dataset.rule](input.value));
        });
    };

    input.addEventListener('input', update);
    update();
});
