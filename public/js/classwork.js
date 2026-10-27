// Teacher tools on the class page: question builder, add/remove people, grading.

import { avatar, debounce, el, errorMessage, toast } from './core.js';
import { submit } from './forms.js';

const MAX_QUESTIONS = 50;

export function initQuestionBuilder() {
    const count = document.querySelector('[data-question-count]');
    const list = document.querySelector('[data-question-list]');
    if (!count || !list) return;

    // Keeps typed text when the number of items changes.
    const render = () => {
        const n = Math.min(Math.max(parseInt(count.value, 10) || 1, 1), MAX_QUESTIONS);
        const current = [...list.querySelectorAll('input')].map((input) => input.value);
        list.replaceChildren(
            ...Array.from({ length: n }, (_, i) =>
                el('div', { class: 'input-group' }, [
                    el('span', { class: 'input-group-text', id: `q-label-${i}` }, `${i + 1}.`),
                    el('input', {
                        type: 'text',
                        name: 'question[]',
                        class: 'form-control',
                        maxlength: '1000',
                        placeholder: `Question ${i + 1}`,
                        'aria-labelledby': `q-label-${i}`,
                        value: current[i] ?? '',
                        required: true,
                    }),
                ])
            )
        );
    };

    count.addEventListener('input', render);
    count.form.addEventListener('reset', () => setTimeout(() => { count.value = 1; render(); }));
    render();
}

export function initPeoplePicker() {
    const form = document.querySelector('[data-people-form]');
    if (!form) return;

    const search = form.querySelector('[data-people-search]');
    const results = form.querySelector('[data-people-results]');
    const chips = form.querySelector('[data-people-selected]');
    const chipsLabel = form.querySelector('[data-people-selected-label]');
    const selected = new Map();
    let lastQuery = '';

    const renderChips = () => {
        chips.replaceChildren(
            ...[...selected.values()].map((user) =>
                el('span', { class: 'pill pill-accent' }, [
                    user.email,
                    el('button', { type: 'button', 'aria-label': `Remove ${user.email}`, 'data-remove-id': user.id }, '×'),
                ])
            )
        );
        chipsLabel.hidden = selected.size === 0;
    };

    const renderResults = (users) => {
        const fresh = users.filter((u) => !selected.has(u.id));
        if (!fresh.length) {
            results.replaceChildren(el('li', { class: 'text-muted-2 justify-content-center' }, 'No one new matches that email.'));
            return;
        }
        results.replaceChildren(
            ...fresh.map((user) =>
                el('li', {}, [
                    avatar(user, 'sm'),
                    el('span', { class: 'who' }, [user.name, el('small', {}, `${user.email} · ${user.role}`)]),
                    el('button', { type: 'button', class: 'btn btn-sm btn-quiet', 'data-add-id': user.id }, 'Add'),
                ])
            )
        );
        results._users = fresh;
    };

    const lookup = debounce((query) => {
        if (query.length < 3) {
            results.replaceChildren();
            return;
        }
        $.getJSON(form.dataset.searchUrl, { data: query })
            .done(({ data }) => query === lastQuery && renderResults(data))
            .fail((xhr) => toast(errorMessage(xhr), 'error'));
    }, 250);

    search.addEventListener('input', () => {
        lastQuery = search.value.trim();
        lookup(lastQuery);
    });

    results.addEventListener('click', (e) => {
        const id = Number(e.target.closest('[data-add-id]')?.dataset.addId);
        const user = results._users?.find((u) => u.id === id);
        if (!user) return;
        selected.set(user.id, user);
        renderChips();
        renderResults(results._users);
        search.focus();
    });

    chips.addEventListener('click', (e) => {
        const id = Number(e.target.closest('[data-remove-id]')?.dataset.removeId);
        if (!id) return;
        selected.delete(id);
        renderChips();
    });

    // Submit the chosen ids rather than the raw form fields.
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        e.stopImmediatePropagation();
        submit(form, { 'added_users[]': [...selected.keys()] });
    }, true);
}

export function initUnenroll() {
    const form = document.querySelector('[data-unenroll-form]');
    const button = form?.querySelector('[data-unenroll-submit]');
    if (!button) return;

    const update = () => {
        const n = form.querySelectorAll('[data-unenroll-checkbox]:checked').length;
        button.classList.toggle('d-none', n === 0);
        button.querySelector('[data-unenroll-count]').textContent = n ? `(${n})` : '';
    };

    form.addEventListener('change', (e) => e.target.matches('[data-unenroll-checkbox]') && update());

    form.addEventListener('submit', (e) => {
        const n = form.querySelectorAll('[data-unenroll-checkbox]:checked').length;
        if (!window.confirm(`Remove ${n} student${n === 1 ? '' : 's'} from this class? Their submitted work will be deleted.`)) {
            e.preventDefault();
            e.stopImmediatePropagation();
        }
    }, true);
}

/** Grades save shortly after the teacher stops typing. */
export function initGrading() {
    const timers = new WeakMap();

    $(document).on('input', '[data-grade-input]', function () {
        const input = this;
        clearTimeout(timers.get(input));
        timers.set(input, setTimeout(() => saveGrade(input), 500));
    });
}

function saveGrade(input) {
    const state = input.closest('.panel-body').querySelector('[data-save-state]');
    const max = Number(input.max);
    const value = input.value.trim();

    if (value !== '' && (Number(value) < 0 || Number(value) > max || !Number.isInteger(Number(value)))) {
        input.classList.add('is-invalid');
        setState(state, `0–${max} only`, 'is-error');
        return;
    }

    input.classList.remove('is-invalid');
    setState(state, 'Saving…');

    $.post(input.dataset.url, { answer_id: input.dataset.answerId, grade: value })
        .done(() => setState(state, 'Saved', 'is-saved'))
        .fail((xhr) => {
            input.classList.add('is-invalid');
            setState(state, errorMessage(xhr, 'Not saved'), 'is-error');
        });
}

function setState(node, text, cls = '') {
    if (!node) return;
    node.textContent = text;
    node.className = `save-state ${cls}`;
}
