// Shared helpers: AJAX defaults, toasts, safe DOM building.

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

export function setupAjax() {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' },
        beforeSend(xhr) {
            // Lets the server skip broadcasting a chat message back to its sender.
            const socketId = window.Echo?.socketId?.();
            if (socketId) xhr.setRequestHeader('X-Socket-ID', socketId);
        },
    });
}

/** Human-readable message for a failed jQuery XHR. */
export function errorMessage(xhr, fallback = 'Something went wrong. Please try again.') {
    const body = xhr?.responseJSON ?? {};
    if (xhr?.status === 419) return 'Your session expired. Reload the page and try again.';
    if (xhr?.status === 0) return 'You appear to be offline. Check your connection and try again.';
    if (body.error) return body.error;
    if (body.errors) return Object.values(body.errors).flat()[0] ?? fallback;
    if (body.message && xhr.status < 500) return body.message;
    return fallback;
}

export function toast(message, type = 'success') {
    const stack = document.getElementById('toastStack');
    if (!stack || !message) return;

    const note = el('div', { class: `toast-note${type === 'error' ? ' is-error' : ''}` }, [
        el('i', { class: type === 'error' ? 'fa-solid fa-circle-exclamation' : 'fa-solid fa-circle-check', 'aria-hidden': 'true' }),
        el('span', {}, message),
    ]);
    stack.append(note);

    setTimeout(() => {
        note.classList.add('is-leaving');
        setTimeout(() => note.remove(), 250);
    }, type === 'error' ? 6000 : 3500);
}

/**
 * Tiny element builder. Children given as strings become text nodes, so
 * user-supplied content is never parsed as HTML.
 */
export function el(tag, attrs = {}, children = []) {
    const node = document.createElement(tag);
    for (const [key, value] of Object.entries(attrs)) {
        if (value === false || value == null) continue;
        node.setAttribute(key, value === true ? '' : value);
    }
    for (const child of [].concat(children)) {
        if (child == null) continue;
        node.append(child instanceof Node ? child : document.createTextNode(String(child)));
    }
    return node;
}

/** Avatar matching the Blade <x-avatar> component. */
export function avatar(user, size = 'sm') {
    const span = el('span', { class: `avatar avatar-${size}` });
    if (user?.avatar_url) {
        span.append(el('img', { src: user.avatar_url, alt: '', loading: 'lazy' }));
    } else {
        span.append(el('span', { 'aria-hidden': 'true' }, user?.initial ?? '?'));
    }
    return span;
}

export function debounce(fn, wait = 300) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), wait);
    };
}

/** Show flash messages from the server (session('success'/'error')) or a previous page. */
export function showFlashes() {
    document.querySelectorAll('template[data-flash]').forEach((tpl) => {
        toast(tpl.content.textContent.trim(), tpl.dataset.flash);
        tpl.remove();
    });

    try {
        const pending = sessionStorage.getItem('pendingToast');
        if (pending) {
            sessionStorage.removeItem('pendingToast');
            toast(pending);
        }
    } catch {
        // Storage can be unavailable (private mode); the toast is optional.
    }
}
