// Mobile buy bar: visible from the first scroll, hidden while the checkout form itself is on screen.
const bar = document.querySelector('[data-buy-bar]');
const target = document.getElementById('checkout');

if (bar && target && 'IntersectionObserver' in window) {
    let formVisible = false;
    let scrolled = window.scrollY > 200;

    const apply = () => {
        bar.hidden = !(scrolled && !formVisible);
    };

    new IntersectionObserver((entries) => {
        formVisible = entries[0].isIntersecting;
        apply();
    }).observe(target);

    window.addEventListener('scroll', () => {
        scrolled = window.scrollY > 200;
        apply();
    }, { passive: true });

    apply();
}
