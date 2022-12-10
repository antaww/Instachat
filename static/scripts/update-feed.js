function addInfiniteFeed() {
    const feedContainer = document.querySelector('.feed-container');
    const lastChild = feedContainer.lastElementChild;
    let offset = 5;

    const observer = new IntersectionObserver(async entries => {
        if (entries[0].isIntersecting) {
            const url = `${window.location.origin}/getFeed?offset=${offset}`;
            const request = await fetch(url, {
                method: 'GET',
                credentials: 'include',
            });

            const text = await request.text();
            const div = document.createElement('div');
            div.innerHTML = text;
            for (const child of div.children) {
                const menuChild = child.querySelector('.post-menu');
                if (!menuChild) continue;
                menuChild.addEventListener('click', () => showMenu(menuChild));
            }

            if (div.children.length > 0) {
                feedContainer.appendChild(...div.children);
                twemoji.parse(document.body);
                offset += 5;
            }
        }
    });

    observer.observe(lastChild);
}

document.addEventListener("DOMContentLoaded", () => {
    addInfiniteFeed();

    const postMenus = getPostMenus();
    postMenus.forEach(menu => menu.addEventListener('click', () => showMenu(menu)));

    document.addEventListener('click', e => {
        if (e.target.closest('.menu-container') || e.target.closest('.post-menu')) return;

        const postMenus = getPostMenus();
        postMenus.forEach(menu => menu.nextElementSibling.classList.add('menu-hidden'));
    });
});

function showMenu(menu) {
    hideOthersMenu(menu);
    const nextMenuContainer = menu.nextElementSibling;
    nextMenuContainer.classList.toggle('menu-hidden');
    nextMenuContainer.style.left = `${menu.getBoundingClientRect().right - (menu.offsetWidth + nextMenuContainer.offsetWidth + 10)}px`;
    nextMenuContainer.style.top = `${menu.offsetTop}px`;
    nextMenuContainer.addEventListener('click', e => {
        if (e.target.classList.contains('menu-delete-btn')) {
            if ([...document.querySelectorAll('dialog')].some(dialog => dialog.open)) return;

            const modal = nextMenuContainer.querySelector('dialog');
            modal.showModal();
            if (modal.open) {
                modal.querySelector('.modal-cancel-btn').addEventListener('click', () => modal.close());
            }
        }
    });
}

function hideOthersMenu(menu) {
    const postMenus = getPostMenus();
    postMenus.forEach(e => {
        if (e !== menu) {
            e.nextElementSibling.classList.add('menu-hidden');
        }
    });
}

function getPostMenus() {
    return document.querySelectorAll('.post-menu');
}

function addClickEvent(post) {
    const postMenu = post.querySelector('.post-right-part');
    postMenu.addEventListener('click', e => {
        /**
         * @type {EventTarget & HTMLElement}
         */
        const target = e.target;
        if (['a', 'button'].includes(target.tagName)) return;
        window.location.href = `${window.location.origin}/post?id=${post.dataset.id}`;
    });
}
