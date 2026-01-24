document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('[data-dropdown-toggle="pixel-user-menu"]');
    const menu = document.getElementById('pixel-user-menu');

    if (!toggle || !menu) return;

    const close = () => {
        menu.classList.add('hidden');
        menu.setAttribute('aria-hidden', 'true');
        toggle.setAttribute('aria-expanded', 'false');
    };

    const open = () => {
        menu.classList.remove('hidden');
        menu.setAttribute('aria-hidden', 'false');
        toggle.setAttribute('aria-expanded', 'true');
    };

    toggle.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (menu.classList.contains('hidden')) open();
        else close();
    });

    document.addEventListener('click', () => close());
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
    });
});
