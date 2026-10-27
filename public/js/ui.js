// Page chrome: sidebar drawer, tabs, copy buttons, password toggles,
// confirmation dialog and the profile photo picker.

import { errorMessage, toast } from './core.js';
import { navigate } from './forms.js';

export function initSidebar() {
    const toggle = document.querySelector('[data-sidebar-toggle]');
    if (!toggle) return;

    const setOpen = (open) => {
        document.body.classList.toggle('sidebar-open', open);
        toggle.setAttribute('aria-expanded', String(open));
    };

    toggle.addEventListener('click', () => setOpen(!document.body.classList.contains('sidebar-open')));
    document.querySelector('[data-sidebar-close]')?.addEventListener('click', () => setOpen(false));
    document.addEventListener('keydown', (e) => e.key === 'Escape' && setOpen(false));
}

/** Keep the active class tab in the URL hash so links and reloads land on it. */
export function initTabs() {
    const tabs = document.querySelectorAll('[data-bs-toggle="tab"][data-tab]');
    if (!tabs.length) return;

    const show = (name) => {
        const tab = document.querySelector(`[data-bs-toggle="tab"][data-tab="${CSS.escape(name)}"]`);
        if (tab) bootstrap.Tab.getOrCreateInstance(tab).show();
    };

    tabs.forEach((tab) => {
        tab.addEventListener('shown.bs.tab', () => {
            history.replaceState(null, '', `#${tab.dataset.tab}`);
            document.dispatchEvent(new CustomEvent('tab:shown', { detail: tab.dataset.tab }));
        });
    });

    const initial = window.location.hash.slice(1);
    if (initial) show(initial);
    window.addEventListener('hashchange', () => show(window.location.hash.slice(1)));
}

export function initCopyButtons() {
    $(document).on('click', '[data-copy]', async function () {
        const text = this.dataset.copy;
        try {
            await navigator.clipboard.writeText(text);
            toast(`Copied “${text}” to the clipboard.`);
        } catch {
            window.prompt('Copy the class code:', text);
        }
    });
}

export function initPasswordToggles() {
    $(document).on('click', '[data-toggle-password]', function () {
        const input = document.querySelector(this.dataset.togglePassword);
        if (!input) return;
        const reveal = input.type === 'password';
        input.type = reveal ? 'text' : 'password';
        this.setAttribute('aria-pressed', String(reveal));
        this.setAttribute('aria-label', reveal ? 'Hide password' : 'Show password');
        this.querySelector('i').className = reveal ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
    });
}

/** Focus the first field when any modal opens (autofocus doesn't work in modals). */
export function initModalFocus() {
    document.addEventListener('shown.bs.modal', (event) => {
        event.target.querySelector('input:not([type=hidden]):not([type=file]):not(.visually-hidden), textarea, select')?.focus();
    });
}

/**
 * Any element with data-confirm-url opens the shared confirm dialog and,
 * on accept, POSTs to that URL. Fires "classes-changed" on success unless
 * the response asks for a redirect.
 */
export function initConfirm() {
    const modalEl = document.getElementById('confirmModal');
    if (!modalEl) return;

    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    const accept = modalEl.querySelector('[data-confirm-accept]');
    let url = null;

    $(document).on('click', '[data-confirm-url]', function () {
        url = this.dataset.confirmUrl;
        modalEl.querySelector('#confirmModalLabel').textContent = this.dataset.confirmTitle ?? 'Are you sure?';
        modalEl.querySelector('#confirmModalBody').textContent = this.dataset.confirmBody ?? '';
        accept.textContent = this.dataset.confirmLabel ?? 'Confirm';
        accept.className = `btn ${this.hasAttribute('data-confirm-danger') ? 'btn-danger' : 'btn-primary'}`;
        modal.show();
    });

    accept.addEventListener('click', () => {
        if (!url) return;
        accept.disabled = true;

        $.post(url)
            .done((response) => {
                modal.hide();
                if (response.redirectUrl) {
                    navigate(response.redirectUrl, response.success);
                } else {
                    toast(response.success);
                    document.dispatchEvent(new CustomEvent('classes-changed', { detail: response }));
                }
            })
            .fail((xhr) => toast(errorMessage(xhr), 'error'))
            .always(() => {
                accept.disabled = false;
                url = null;
            });
    });
}

/** Live preview for the profile photo before it is uploaded. */
export function initAvatarPicker() {
    const input = document.querySelector('[data-avatar-input]');
    if (!input) return;

    const preview = document.querySelector('[data-avatar-preview]');
    const save = document.querySelector('[data-avatar-save]');
    let objectUrl = null;

    input.addEventListener('change', () => {
        const file = input.files?.[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            toast('Please choose an image file.', 'error');
            input.value = '';
            return;
        }

        if (objectUrl) URL.revokeObjectURL(objectUrl);
        objectUrl = URL.createObjectURL(file);

        const img = document.createElement('img');
        img.src = objectUrl;
        img.alt = '';
        preview.replaceChildren(img);
        save.disabled = false;
    });
}
