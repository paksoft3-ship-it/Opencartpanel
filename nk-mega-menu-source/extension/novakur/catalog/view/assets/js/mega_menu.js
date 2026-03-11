document.addEventListener('DOMContentLoaded', () => {
    const items = document.querySelectorAll('.nk-mega-menu__item');
    let timeout;

    items.forEach(item => {
        item.addEventListener('mouseenter', () => {
            clearTimeout(timeout);
            items.forEach(i => i.classList.remove('is-open'));
            item.classList.add('is-open');
        });

        item.addEventListener('mouseleave', () => {
            timeout = setTimeout(() => {
                item.classList.remove('is-open');
            }, 150);
        });

        item.addEventListener('focusin', () => {
            clearTimeout(timeout);
            items.forEach(i => i.classList.remove('is-open'));
            item.classList.add('is-open');
        });

        item.addEventListener('focusout', (e) => {
            if (!item.contains(e.relatedTarget)) {
                item.classList.remove('is-open');
            }
        });
    });

    const topLinks = document.querySelectorAll('.nk-mega-menu__item > a');
    topLinks.forEach((link, index) => {
        link.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight') {
                e.preventDefault();
                const next = topLinks[index + 1];
                if (next) next.focus();
            } else if (e.key === 'ArrowLeft') {
                e.preventDefault();
                const prev = topLinks[index - 1];
                if (prev) prev.focus();
            }
        });
    });
});
