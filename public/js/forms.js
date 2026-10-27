// Every <form data-ajax-form> is submitted with fetch-style AJAX.
//
// Server contract (see App\Http\Controllers\Controller):
//   200 { success, redirectUrl? }      -> toast, then redirect or fire event
//   4xx { error }                       -> message in .form-alert
//   422 { errors: { field: [msg] } }    -> message under each field
//
// Options on the form element:
//   data-on-success="classes-changed"  dispatch a DOM event instead of navigating
//   data-reset-on-success              clear the form after success

import { errorMessage, toast } from './core.js';

export function initForms() {
    $(document).on('submit', 'form[data-ajax-form]', function (event) {
        event.preventDefault();
        submit(this);
    });

    // Clear a field's error as soon as the user edits it.
    $(document).on('input change', 'form[data-ajax-form] :input', function () {
        clearFieldError(this.form, this.name);
    });
}

export function submit(form, extraData = {}) {
    const button = form.querySelector('[type="submit"]');
    if (button?.disabled && button.dataset.busy) return;

    clearErrors(form);
    const data = new FormData(form);
    for (const [key, value] of Object.entries(extraData)) {
        [].concat(value).forEach((v) => data.append(key, v));
    }

    setBusy(button, true);

    return $.ajax({
        url: form.getAttribute('action'),
        method: 'POST',
        data,
        processData: false,
        contentType: false,
    })
        .done((response) => {
            if (response.error) {
                showAlert(form, response.error);
                setBusy(button, false);
                return;
            }

            if (form.dataset.onSuccess) {
                toast(response.success);
                setBusy(button, false);
                form.reset();
                bootstrap.Modal.getInstance(form.closest('.modal'))?.hide();
                document.dispatchEvent(new CustomEvent(form.dataset.onSuccess, { detail: response }));
                return;
            }

            if (response.redirectUrl) {
                navigate(response.redirectUrl, response.success);
                return;
            }

            toast(response.success);
            setBusy(button, false);
            if (form.hasAttribute('data-reset-on-success')) form.reset();
        })
        .fail((xhr) => {
            setBusy(button, false);
            const fields = xhr.responseJSON?.errors;
            if (xhr.status === 422 && fields) {
                showFieldErrors(form, fields);
            } else {
                showAlert(form, errorMessage(xhr));
            }
        });
}

/**
 * Go to a URL, reloading when only the hash differs (so new data shows).
 * The message is shown as a toast once the next page has loaded.
 */
export function navigate(url, message = null) {
    if (message) {
        try { sessionStorage.setItem('pendingToast', message); } catch { toast(message); }
    }
    const target = new URL(url, window.location.href);
    const here = new URL(window.location.href);
    window.location.href = target.href;
    if (target.pathname === here.pathname && target.search === here.search) {
        window.location.reload();
    }
}

function setBusy(button, busy) {
    if (!button) return;
    if (busy) {
        button.dataset.label = button.innerHTML;
        button.dataset.busy = '1';
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span><span>Working…</span>';
    } else if (button.dataset.busy) {
        button.innerHTML = button.dataset.label;
        button.disabled = false;
        delete button.dataset.busy;
    }
}

function showAlert(form, message) {
    const slot = form.querySelector('.form-alert');
    if (!slot) {
        toast(message, 'error');
        return;
    }
    slot.replaceChildren();
    const alert = document.createElement('div');
    alert.className = 'alert alert-danger';
    alert.setAttribute('role', 'alert');
    alert.textContent = message;
    slot.append(alert);
}

function showFieldErrors(form, errors) {
    let firstInvalid = null;
    let unplaced = [];

    for (const [key, messages] of Object.entries(errors)) {
        // "question.2" -> base name "question" for array inputs.
        const base = key.split('.')[0];
        const inputs = form.querySelectorAll(`[name="${base}"], [name="${base}[]"]`);
        const feedback = form.querySelector(`[data-error-for="${base}"]`);

        inputs.forEach((input) => input.classList.add('is-invalid'));
        form.querySelector(`[data-error-group="${base}"]`)?.classList.add('is-invalid');

        if (feedback) {
            feedback.textContent = messages[0];
            firstInvalid ??= inputs[0] ?? null;
        } else {
            unplaced.push(messages[0]);
        }
    }

    if (unplaced.length) showAlert(form, unplaced.join(' '));
    firstInvalid?.focus();
}

function clearFieldError(form, name) {
    if (!form || !name) return;
    const base = name.replace('[]', '');
    form.querySelectorAll(`[name="${name}"]`).forEach((input) => input.classList.remove('is-invalid'));
    form.querySelector(`[data-error-group="${base}"]`)?.classList.remove('is-invalid');
    const feedback = form.querySelector(`[data-error-for="${base}"]`);
    if (feedback) feedback.textContent = '';
}

function clearErrors(form) {
    form.querySelectorAll('.is-invalid').forEach((node) => node.classList.remove('is-invalid'));
    form.querySelectorAll('[data-error-for]').forEach((node) => (node.textContent = ''));
    form.querySelector('.form-alert')?.replaceChildren();
}
