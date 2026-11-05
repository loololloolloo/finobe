(() => {
    'use strict';
    const root = document.documentElement;
    const body = document.body;
    const overlay = document.getElementById('blog-search');
    const query = document.getElementById('blog-search-query');
    let previousFocus = null;
    const openSearch = () => {
        if (!overlay) return;
        previousFocus = document.activeElement;
        overlay.hidden = false;
        body.classList.add('blog-search-open');
        if (query) query.focus();
    };
    const closeSearch = () => {
        if (!overlay) return;
        overlay.hidden = true;
        body.classList.remove('blog-search-open');
        if (previousFocus && typeof previousFocus.focus === 'function') previousFocus.focus();
    };
    document.querySelectorAll('[data-blog-search]').forEach(button => {
        button.addEventListener('click', openSearch);
    });
    const close = document.querySelector('[data-blog-search-close]');
    if (close) close.addEventListener('click', closeSearch);
    if (overlay) overlay.addEventListener('click', event => {
        if (event.target === overlay) closeSearch();
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            if (overlay && !overlay.hidden) closeSearch();
            else closeMenu();
        }
        if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();
            openSearch();
        }
    });
    const menu = document.querySelector('[data-blog-menu]');
    function closeMenu() {
        body.classList.remove('gh-head-open');
        root.classList.remove('gh-navigation-open');
        if (menu) menu.setAttribute('aria-expanded', 'false');
    }
    if (menu) menu.addEventListener('click', () => {
        const open = !body.classList.contains('gh-head-open');
        body.classList.toggle('gh-head-open', open);
        root.classList.toggle('gh-navigation-open', open);
        menu.setAttribute('aria-expanded', String(open));
    });
})();
